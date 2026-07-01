<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$visitUsContent = require __DIR__ . '/content/visit_us.php';
$page = Page::getByPath('/visit-us', 'ACTIVE');
$legacyPage = null;

if (!$page instanceof Page || $page->isError()) {
    $legacyPage = Page::getByPath('/im-new', 'ACTIVE');
}

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
            'cName' => $visitUsContent['name'],
            'cHandle' => 'visit-us',
            'cDescription' => $visitUsContent['description'],
        ],
        $fullTemplate
    );

    if ($legacyPage instanceof Page && !$legacyPage->isError()) {
        $legacyPage->moveToTrash();
        $output->writeln('<info>Created /visit-us and moved /im-new to trash.</info>');
    } else {
        $output->writeln('<info>Created /visit-us.</info>');
    }
}

$page->update([
    'cName' => $visitUsContent['name'],
    'cHandle' => 'visit-us',
    'cDescription' => $visitUsContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $visitUsContent['content']]);

$output->writeln('<info>Updated Visit Us page content from the leadership questionnaire.</info>');

return 0;
