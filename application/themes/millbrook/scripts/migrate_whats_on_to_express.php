<?php

use Application\Block\WhatsOnBlock\Controller as WhatsOnBlockController;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = app(EntityManagerInterface::class);
$objectManager = app(ObjectManager::class);

$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'whats_on_item']);

if (!$entity) {
    $builder = $objectManager->buildObject('whats_on_item', 'whats_on_items', 'What’s On Item');
    $builder->setDescription('Shared What’s On items for visitor-facing pages.');
    $builder->setSupportsCustomDisplayOrder(true);
    $builder->setIncludeInPublicList(true);
    $builder->setIsPublished(true);
    $builder->addAttribute('text', 'Eyebrow', 'eyebrow');
    $builder->addAttribute('text', 'Item Title', 'item_title');
    $builder->addAttribute('textarea', 'Summary', 'summary');
    $builder->addAttribute('text', 'Link Label', 'link_label');
    $builder->addAttribute('url', 'Link URL', 'link_url');
    $entity = $builder->save();
    $output->writeln('<info>Created Express entity: What’s On Item</info>');
}

$entryList = new EntryList($entity);
$entryList->ignorePermissions();
$existingEntries = $entryList->getResults();

if ($existingEntries) {
    $output->writeln('<comment>What’s On Item entries already exist. Leaving them in place.</comment>');
    return 0;
}

$sourceItems = get_whats_on_source_items();
if ($sourceItems === []) {
    $output->writeln('<comment>No source What’s On items were found to migrate.</comment>');
    return 0;
}

$created = 0;

foreach ($sourceItems as $item) {
    $entryBuilder = $objectManager->buildEntry($entity);
    $entryBuilder->setAttribute('eyebrow', (string) ($item['eyebrow'] ?? ''));
    $entryBuilder->setAttribute('item_title', (string) ($item['title'] ?? ''));
    $entryBuilder->setAttribute('summary', (string) ($item['summary'] ?? ''));
    $entryBuilder->setAttribute('link_label', (string) ($item['linkLabel'] ?? ''));
    $entryBuilder->setAttribute('link_url', (string) ($item['linkUrl'] ?? ''));
    $entryBuilder->save();
    $created++;
}

$output->writeln(sprintf('<info>Migrated %d What’s On items into Express.</info>', $created));

return 0;

function get_whats_on_source_items(): array
{
    $sourcePage = Page::getByPath('/community/whats-on', 'ACTIVE');
    if ($sourcePage instanceof Page && !$sourcePage->isError()) {
        $items = get_whats_on_items_from_area($sourcePage, 'Main');
        if ($items !== []) {
            return $items;
        }
    }

    $site = \Core::make('site')->getSite();
    $homePage = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    if ($homePage instanceof Page && !$homePage->isError()) {
        $items = get_whats_on_items_from_area($homePage, 'Home Visit Card');
        if ($items !== []) {
            return $items;
        }
    }

    return [
        [
            'eyebrow' => 'Sunday',
            'title' => 'Worship at 11:00am',
            'summary' => 'A welcoming Sunday gathering with worship, prayer, Bible teaching, and time together afterwards.',
            'linkLabel' => 'Plan your visit',
            'linkUrl' => '/visit-us',
        ],
        [
            'eyebrow' => 'Midweek',
            'title' => 'Homegroups, prayer, and shared life',
            'summary' => 'Smaller gatherings through the week help people build friendships, pray together, and keep growing in faith.',
            'linkLabel' => 'Explore church life',
            'linkUrl' => '/community',
        ],
        [
            'eyebrow' => 'Families',
            'title' => 'Children and families are welcome',
            'summary' => 'Children are a valued part of church life, with support for families and age-appropriate opportunities to belong.',
            'linkLabel' => 'Children & families',
            'linkUrl' => '/community/children',
        ],
        [
            'eyebrow' => 'Looking ahead',
            'title' => 'Seasonal events and church-wide gatherings',
            'summary' => 'Special services, shared meals, and occasional events help mark the year together as a church family.',
            'linkLabel' => 'Get in touch',
            'linkUrl' => '/contact',
        ],
    ];
}

function get_whats_on_items_from_area(Page $page, string $areaHandle): array
{
    $area = Area::getOrCreate($page, $areaHandle);
    foreach ($area->getAreaBlocksArray($page) as $block) {
        if (!$block instanceof Block || $block->getBlockTypeHandle() !== 'whats_on_block') {
            continue;
        }

        $controller = $block->getController();
        if ($controller instanceof WhatsOnBlockController) {
            $itemsJson = trim((string) ($controller->itemsJson ?? ''));
            if ($itemsJson === '') {
                continue;
            }

            $items = json_decode($itemsJson, true);
            if (is_array($items)) {
                return array_values(array_filter($items, static function ($item): bool {
                    return is_array($item) && (trim((string) ($item['title'] ?? '')) !== '' || trim((string) ($item['summary'] ?? '')) !== '');
                }));
            }
        }
    }

    return [];
}
