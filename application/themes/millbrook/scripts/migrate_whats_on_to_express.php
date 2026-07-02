<?php

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;

$entityManager = app(EntityManagerInterface::class);
$objectManager = app(ObjectManager::class);

$attributeDefinitions = whats_on_attribute_definitions();
$formAttributeHandles = array_keys($attributeDefinitions);

$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'whats_on_item']);

if (!$entity) {
    $builder = $objectManager->buildObject('whats_on_item', 'whats_on_items', 'What’s On Item');
    $builder->setDescription('Shared What’s On items for visitor-facing pages.');
    $builder->setSupportsCustomDisplayOrder(true);
    $builder->setIncludeInPublicList(true);
    $builder->setIsPublished(true);
    $builder->setLabelMask('Item Title: {{item_title}}');

    foreach ($attributeDefinitions as $handle => $definition) {
        $builder->addAttribute($definition['type'], $definition['name'], $handle);
    }

    $formBuilder = $builder->buildForm('What’s On Details');
    $fieldset = $formBuilder->addFieldSet('Item details');
    foreach ($formAttributeHandles as $handle) {
        $fieldset->addAttributeKeyControl($handle);
    }
    $formBuilder->save();

    $entity = $builder->save();
    $output->writeln('<info>Created Express entity: What’s On Item</info>');
} else {
    $entity->setDescription('Shared What’s On items for visitor-facing pages.');
    $entity->setSupportsCustomDisplayOrder(true);
    $entity->setIncludeInPublicList(true);
    $entity->setIsPublished(true);
    $entity->setLabelMask('Item Title: {{item_title}}');
    $entityManager->persist($entity);
    $entityManager->flush();

    foreach ($attributeDefinitions as $handle => $definition) {
        ensure_whats_on_attribute($entity, $definition['type'], $definition['name'], $handle);
    }

    $entityManager->refresh($entity);
    ensure_whats_on_form($entity, $formAttributeHandles);
}

$seedItems = whats_on_seed_items();
$existingEntries = whats_on_existing_entries_by_title($entity);
$renamedLegacyTitles = [
    'Worship at 11:00am' => 'Sunday worship',
    'Homegroups, prayer, and shared life' => 'Homegroups',
    'Children and families are welcome' => 'Sunday School',
    'Seasonal events and church-wide gatherings' => 'Special services',
];
$created = 0;
$updated = 0;

foreach ($seedItems as $item) {
    $entry = $existingEntries[$item['title']] ?? null;

    if (!$entry) {
        foreach ($renamedLegacyTitles as $legacyTitle => $newTitle) {
            if ($newTitle === $item['title'] && isset($existingEntries[$legacyTitle])) {
                $entry = $existingEntries[$legacyTitle];
                break;
            }
        }
    }

    if (!$entry instanceof Entry) {
        $entry = $objectManager->buildEntry($entity)->save();
        $created++;
    } else {
        $updated++;
    }

    apply_whats_on_item_to_entry($entry, $item);
    $entry->setEntryDisplayOrder((int) $item['sort_order']);
    $entityManager->persist($entry);
}

$entityManager->flush();

$output->writeln(sprintf(
    '<info>Updated What’s On Express source: %d created, %d updated.</info>',
    $created,
    $updated
));

return 0;

function whats_on_attribute_definitions(): array
{
    return [
        'section' => ['type' => 'text', 'name' => 'Section (weekly, monthly, seasonal, special)'],
        'eyebrow' => ['type' => 'text', 'name' => 'Eyebrow'],
        'item_title' => ['type' => 'text', 'name' => 'Item Title'],
        'summary' => ['type' => 'textarea', 'name' => 'Summary'],
        'meta' => ['type' => 'text', 'name' => 'Meta / short detail'],
        'link_label' => ['type' => 'text', 'name' => 'Link Label'],
        'link_url' => ['type' => 'url', 'name' => 'Link URL'],
        'card_style' => ['type' => 'text', 'name' => 'Card Colour (blue, lime, purple, coral, dark)'],
        'always_on' => ['type' => 'boolean', 'name' => 'Always On'],
        'start_date' => ['type' => 'date_time', 'name' => 'Show From'],
        'end_date' => ['type' => 'date_time', 'name' => 'Show Until'],
        'sort_order' => ['type' => 'number', 'name' => 'Sort Order'],
    ];
}

function ensure_whats_on_attribute(Entity $entity, string $typeHandle, string $name, string $handle): ?ExpressKey
{
    $entityManager = app(EntityManagerInterface::class);
    $category = $entity->getAttributeKeyCategory();
    $existing = $category->getAttributeKeyByHandle($handle);

    if ($existing instanceof ExpressKey) {
        $existing->setAttributeKeyName($name);
        app(EntityManagerInterface::class)->persist($existing);
        app(EntityManagerInterface::class)->flush();

        return $existing;
    }

    $typeFactory = app(TypeFactory::class);
    $type = $typeFactory->getByHandle($typeHandle);
    if (!$type) {
        return null;
    }

    $key = new ExpressKey();
    $key->setEntity($entity);
    $key->setAttributeKeyHandle($handle);
    $key->setAttributeKeyName($name);
    $key->setAttributeType($type);
    $key->setIsAttributeKeySearchable(true);
    $key->setIsAttributeKeyContentIndexed(false);

    $settings = $type->getController()->getAttributeKeySettings();
    if ($settings instanceof Settings) {
        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);
        $entityManager->persist($settings);
    }

    $entity->getAttributes()->add($key);
    $entityManager->persist($key);
    $entityManager->persist($entity);
    $entityManager->flush();

    $category = app(ExpressCategory::class, ['entity' => $entity]);
    $category->getSearchIndexer()->updateRepositoryColumns($category, $key);

    return $key;
}

function ensure_whats_on_form(Entity $entity, array $attributeHandles): void
{
    $entityManager = app(EntityManagerInterface::class);
    $form = $entity->getDefaultEditForm() ?: $entity->getForm('What’s On Details');

    if (!$form instanceof Form) {
        $form = new Form();
        $form->setName('What’s On Details');
        $form->setEntity($entity);
        $entity->getForms()->add($form);
    }

    $fieldSet = null;
    foreach ($form->getFieldSets() as $existingFieldSet) {
        $fieldSet = $existingFieldSet;
        break;
    }

    if (!$fieldSet instanceof FieldSet) {
        $fieldSet = new FieldSet();
        $fieldSet->setTitle('Item details');
        $fieldSet->setPosition(0);
        $fieldSet->setForm($form);
        $form->getFieldSets()->add($fieldSet);
    }

    $existingControlHandles = [];
    foreach ($form->getControls() as $control) {
        if ($control instanceof AttributeKeyControl && $control->getAttributeKey()) {
            $existingControlHandles[] = $control->getAttributeKey()->getAttributeKeyHandle();
        }
    }

    $position = count($fieldSet->getControls());
    foreach ($attributeHandles as $handle) {
        if (in_array($handle, $existingControlHandles, true)) {
            continue;
        }

        $key = $entity->getAttributeKeyCategory()->getAttributeKeyByHandle($handle);
        if (!$key instanceof ExpressKey) {
            continue;
        }

        $control = new AttributeKeyControl();
        $control->setId((new UuidGenerator())->generate($entityManager, $control));
        $control->setAttributeKey($key);
        $control->setFieldSet($fieldSet);
        $control->setPosition($position);
        $fieldSet->getControls()->add($control);
        $entityManager->persist($control);
        $position++;
    }

    $entity->setDefaultEditForm($form);
    $entity->setDefaultViewForm($form);
    $entityManager->persist($fieldSet);
    $entityManager->persist($form);
    $entityManager->persist($entity);
    $entityManager->flush();
}

function whats_on_existing_entries_by_title(Entity $entity): array
{
    $list = new EntryList($entity);
    $list->ignorePermissions();

    $entries = [];
    foreach ($list->getResults() as $entry) {
        if (!$entry instanceof Entry) {
            continue;
        }

        $title = trim((string) $entry->getAttribute('item_title'));
        if ($title !== '') {
            $entries[$title] = $entry;
        }
    }

    return $entries;
}

function apply_whats_on_item_to_entry(Entry $entry, array $item): void
{
    $entity = $entry->getEntity();

    foreach ([
        'section',
        'eyebrow',
        'item_title',
        'summary',
        'meta',
        'link_label',
        'link_url',
        'card_style',
        'always_on',
        'start_date',
        'end_date',
        'sort_order',
    ] as $handle) {
        $key = find_whats_on_attribute_key($entity, $handle);
        if (!$key instanceof ExpressKey) {
            continue;
        }

        $value = $item[$handle] ?? '';
        $entry->setAttribute($key, $value);
    }
}

function find_whats_on_attribute_key(Entity $entity, string $handle): ?ExpressKey
{
    foreach ($entity->getAttributes() as $key) {
        if ($key instanceof ExpressKey && $key->getAttributeKeyHandle() === $handle) {
            return $key;
        }
    }

    $key = app(ExpressCategory::class, ['entity' => $entity])->getAttributeKeyByHandle($handle);

    return $key instanceof ExpressKey ? $key : null;
}

function whats_on_seed_items(): array
{
    return [
        [
            'section' => 'weekly',
            'eyebrow' => 'Every Sunday',
            'item_title' => 'Sunday worship',
            'title' => 'Sunday worship',
            'summary' => 'We meet at 11:00am for worship, prayer, Bible teaching, and time together.',
            'meta' => 'Everyone welcome | Plan your visit',
            'link_label' => 'Plan your visit',
            'link_url' => '/visit-us',
            'card_style' => 'blue',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 10,
        ],
        [
            'section' => 'weekly',
            'eyebrow' => 'Sunday mornings',
            'item_title' => 'Sunday School',
            'title' => 'Sunday School',
            'summary' => 'Children are included in church life, with Sunday School usually available during term time.',
            'meta' => 'For children | During the service',
            'link_label' => 'Children & families',
            'link_url' => '/community/children',
            'card_style' => 'lime',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 20,
        ],
        [
            'section' => 'weekly',
            'eyebrow' => 'Sunday evenings',
            'item_title' => 'Youth',
            'title' => 'Youth',
            'summary' => 'A relaxed space for secondary school age young people, with snacks, games, teaching, trips, and time together.',
            'meta' => '6:30-8:00pm | Secondary school age',
            'link_label' => 'Youth',
            'link_url' => '/community/youth',
            'card_style' => 'purple',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 30,
        ],
        [
            'section' => 'weekly',
            'eyebrow' => 'Midweek',
            'item_title' => 'Homegroups',
            'title' => 'Homegroups',
            'summary' => 'Informal gatherings to talk about faith, ask questions, pray, and share tea, coffee, and biscuits.',
            'meta' => 'Ask for the address',
            'link_label' => 'Homegroups',
            'link_url' => '/community/homegroups',
            'card_style' => 'blue',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 40,
        ],
        [
            'section' => 'weekly',
            'eyebrow' => 'Community',
            'item_title' => 'Cafe Fit',
            'title' => 'Cafe Fit',
            'summary' => 'A weekly community space that supports wellbeing, connection, and friendship.',
            'meta' => 'Open to the community',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'coral',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 50,
        ],
        [
            'section' => 'weekly',
            'eyebrow' => 'Community',
            'item_title' => 'Community Cafe',
            'title' => 'Community Cafe',
            'summary' => 'A welcoming space to connect with others locally, enjoy conversation, and find support through the week.',
            'meta' => 'Open to the community',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'dark',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 60,
        ],
        [
            'section' => 'monthly',
            'eyebrow' => 'First Sunday',
            'item_title' => 'First Breakfast',
            'title' => 'First Breakfast',
            'summary' => 'On the first Sunday of each month, we usually share breakfast or brunch, hear someone’s story, or welcome a guest speaker.',
            'meta' => 'Food, conversation, and church family',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'blue',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 110,
        ],
        [
            'section' => 'monthly',
            'eyebrow' => 'Monthly',
            'item_title' => 'Men’s Ministry',
            'title' => 'Men’s Ministry',
            'summary' => 'Coffee catch-ups, meals, games, activities, and space for men to connect and encourage one another.',
            'meta' => 'Men aged 18+',
            'link_label' => 'Men’s Ministry',
            'link_url' => '/community/mens-ministry',
            'card_style' => 'dark',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 120,
        ],
        [
            'section' => 'monthly',
            'eyebrow' => 'Monthly',
            'item_title' => 'Abide Women’s Ministry',
            'title' => 'Abide Women’s Ministry',
            'summary' => 'A relaxed gathering for women to pause, connect, enjoy time together, and encourage one another.',
            'meta' => 'Women aged 18+',
            'link_label' => 'Abide Women’s Ministry',
            'link_url' => '/community/womens-ministry',
            'card_style' => 'purple',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 130,
        ],
        [
            'section' => 'seasonal',
            'eyebrow' => 'Christian calendar',
            'item_title' => 'Special services',
            'title' => 'Special services',
            'summary' => 'We mark significant moments together, including Christmas carols, Good Friday, and other seasonal services.',
            'meta' => 'Open to everyone',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'coral',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 210,
        ],
        [
            'section' => 'seasonal',
            'eyebrow' => 'Summer',
            'item_title' => 'Kids Summer Club',
            'title' => 'Kids Summer Club',
            'summary' => 'A seasonal activity for primary school aged children, with registration details shared when booking opens.',
            'meta' => 'Booking usually required',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'lime',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 220,
        ],
        [
            'section' => 'seasonal',
            'eyebrow' => 'Women',
            'item_title' => 'Soul Saturday',
            'title' => 'Soul Saturday',
            'summary' => 'A seasonal gathering for women, with time to connect, be encouraged, and share life together.',
            'meta' => 'For women',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'purple',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 230,
        ],
        [
            'section' => 'seasonal',
            'eyebrow' => 'Community',
            'item_title' => 'Community events',
            'title' => 'Community events',
            'summary' => 'Cinema nights, Acoustic Cafe, celebrations, and other local gatherings happen at different points in the year.',
            'meta' => 'Dates advertised as they come up',
            'link_label' => '',
            'link_url' => '',
            'card_style' => 'blue',
            'always_on' => true,
            'start_date' => '',
            'end_date' => '',
            'sort_order' => 240,
        ],
    ];
}
