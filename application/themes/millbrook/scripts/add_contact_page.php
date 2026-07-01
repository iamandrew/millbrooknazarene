<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');
$formBlockType = BlockType::getByHandle('form');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

if (!$formBlockType) {
    $output->writeln('<error>Legacy form block type is not available.</error>');
    return 1;
}

$contactContent = require __DIR__ . '/content/contact.php';
$page = Page::getByPath('/contact', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$root instanceof Page || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => $contactContent['name'],
            'cHandle' => 'contact',
            'cDescription' => $contactContent['description'],
        ],
        $fullTemplate
    );

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

$form = $contactContent['form'];
$questionSetId = (int) $form['questionSetId'];
$questions = [];

foreach ($form['questions'] as $question) {
    $questions[] = [
        'qsID' => $questionSetId,
        'oldQsID' => $questionSetId,
        'msqID' => 0,
        'question' => $question['question'],
        'inputType' => $question['inputType'],
        'options' => $question['options'] ?? '',
        'position' => $question['position'] ?? 1000,
        'width' => $question['width'] ?? 50,
        'height' => $question['height'] ?? 3,
        'required' => $question['required'] ?? 0,
        'defaultDate' => $question['defaultDate'] ?? '',
        'send_notification_from' => $question['send_notification_from'] ?? 0,
    ];
}

$page->addBlock($formBlockType, $area, [
    'qsID' => $questionSetId,
    'oldQsID' => $questionSetId,
    'questionSetId' => $questionSetId,
    'surveyName' => $form['surveyName'],
    'submitText' => $form['submitText'],
    'notifyMeOnSubmission' => 1,
    'recipientEmail' => $form['recipientEmail'],
    'thankyouMsg' => $form['thankyouMsg'],
    'displayCaptcha' => 0,
    'redirectCID' => 0,
    'addFilesToSet' => 0,
    'questions' => $questions,
]);

$page->addBlock($contentBlockType, $area, ['content' => $contactContent['after_form']]);

$output->writeln('<info>Updated Contact page content and enquiry form.</info>');

return 0;
