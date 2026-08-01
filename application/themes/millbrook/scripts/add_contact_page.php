<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;
use Doctrine\ORM\EntityManagerInterface;

$contentBlockType = BlockType::getByHandle('content');
$formBlockType = BlockType::getByHandle('express_form');

if (!$contentBlockType || !$formBlockType) {
    $output->writeln('<error>Required content or Form block type is not available.</error>');
    return 1;
}

$contactContent = require __DIR__ . '/content/contact.php';
$entityManager = app(EntityManagerInterface::class);
$objectManager = app(ObjectManager::class);
$attributes = [
    'name' => ['type' => 'text', 'name' => 'Your name', 'required' => true],
    'email_address' => ['type' => 'email', 'name' => 'Email address', 'required' => true],
    'phone_number' => ['type' => 'text', 'name' => 'Phone number', 'required' => false],
    'enquiry_type' => ['type' => 'select', 'name' => 'What is your enquiry about?', 'required' => true],
    'message' => ['type' => 'textarea', 'name' => 'Message', 'required' => true],
];

$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'contact_enquiry']);

if (!$entity instanceof Entity) {
    $builder = $objectManager->buildObject('contact_enquiry', 'contact_enquiries', 'Contact Enquiry');
    $builder->setDescription('Contact form submissions from the Millbrook website.');
    $builder->setIncludeInPublicList(false);
    $builder->setIsPublished(true);
    $builder->setLabelMask('Enquiry from {{name}}');

    foreach ($attributes as $handle => $attribute) {
        $builder->addAttribute($attribute['type'], $attribute['name'], $handle);
    }

    $formBuilder = $builder->buildForm('Contact form');
    $fieldset = $formBuilder->addFieldSet('');
    foreach (array_keys($attributes) as $handle) {
        $fieldset->addAttributeKeyControl($handle);
    }
    $formBuilder->save();
    $entity = $builder->save();
    $output->writeln('<info>Created Contact Enquiry Express entity.</info>');
} else {
    $category = app(ExpressCategory::class, ['entity' => $entity]);
    foreach ($attributes as $handle => $attribute) {
        $key = $category->getAttributeKeyByHandle($handle);
        if (!$key instanceof ExpressKey) {
            $key = ensure_contact_attribute($entity, $attribute['type'], $attribute['name'], $handle);
        }
        if ($key instanceof ExpressKey) {
            $key->setAttributeKeyName($attribute['name']);
            $entityManager->persist($key);
        }
    }
    $entityManager->flush();
}

$category = app(ExpressCategory::class, ['entity' => $entity]);
$enquiryType = $category->getAttributeKeyByHandle('enquiry_type');
if ($enquiryType instanceof ExpressKey) {
    $existingOptions = [];
    foreach ($enquiryType->getController()->getOptions() as $option) {
        $existingOptions[] = $option->getSelectAttributeOptionValue();
    }
    $optionList = $enquiryType->getAttributeKeySettings()->getOptionList();
    $displayOrder = count($existingOptions);
    foreach (['General question', 'Planning a visit', 'Prayer or pastoral support', 'Community enquiry', 'Something else'] as $option) {
        if (!in_array($option, $existingOptions, true)) {
            $selectOption = new SelectValueOption();
            $selectOption->setSelectAttributeOptionValue($option);
            $selectOption->setOptionList($optionList);
            $selectOption->setDisplayOrder($displayOrder++);
            $optionList->getOptions()->add($selectOption);
            $entityManager->persist($selectOption);
        }
    }
    $entityManager->flush();
}

$form = $entity->getDefaultEditForm();
if (!$form instanceof Form) {
    $output->writeln('<error>Could not create the Contact Enquiry form.</error>');
    return 1;
}

foreach ($form->getFieldSets() as $fieldSet) {
    $fieldSet->setTitle('');
    $entityManager->persist($fieldSet);
}
$entityManager->flush();

$controls = [];
foreach ($form->getControls() as $control) {
    if ($control instanceof AttributeKeyControl && $control->getAttributeKey() instanceof ExpressKey) {
        $controls[$control->getAttributeKey()->getAttributeKeyHandle()] = $control;
    }
}

$entity->setIsPublished(true);
$entityManager->persist($entity);
foreach ($controls as $handle => $control) {
    $control->setIsRequired($attributes[$handle]['required']);
    $entityManager->persist($control);
}
$entityManager->flush();

if (!isset($controls['email_address'])) {
    $output->writeln('<error>Could not find the email address form field.</error>');
    return 1;
}

$page = Page::getByPath('/contact', 'ACTIVE');
if (!$page instanceof Page || $page->isError()) {
    $site = app('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$root instanceof Page || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add($pageType, [
        'cName' => $contactContent['name'],
        'cHandle' => 'contact',
        'cDescription' => $contactContent['description'],
    ], $fullTemplate);
    $output->writeln('<info>Created /contact.</info>');
}

$page->update([
    'cName' => $contactContent['name'],
    'cHandle' => 'contact',
    'cDescription' => $contactContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $contactContent['pre_form']]);
$page->addBlock($formBlockType, $area, [
    'exFormID' => $form->getId(),
    'submitLabel' => $contactContent['form']['submitText'],
    'notifyMeOnSubmission' => 1,
    'recipientEmail' => $contactContent['form']['recipientEmail'],
    'thankyouMsg' => $contactContent['form']['thankyouMsg'],
    'displayCaptcha' => 1,
    'storeFormSubmission' => 1,
    'redirectCID' => 0,
    'replyToEmailControlID' => $controls['email_address']->getId(),
]);
$page->addBlock($contentBlockType, $area, ['content' => $contactContent['after_form']]);

$output->writeln('<info>Updated Contact page with an Express-backed Form and CAPTCHA enabled.</info>');

return 0;

function ensure_contact_attribute(Entity $entity, string $typeHandle, string $name, string $handle): ?ExpressKey
{
    $entityManager = app(EntityManagerInterface::class);
    $category = $entity->getAttributeKeyCategory();
    $existing = $category->getAttributeKeyByHandle($handle);

    if ($existing instanceof ExpressKey) {
        return $existing;
    }

    $type = app(TypeFactory::class)->getByHandle($typeHandle);
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

    app(ExpressCategory::class, ['entity' => $entity])->getSearchIndexer()->updateRepositoryColumns(
        app(ExpressCategory::class, ['entity' => $entity]),
        $key
    );

    return $key;
}
