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

$churchLifeContent = require __DIR__ . '/content/church_life.php';
$page = Page::getByPath('/community', 'ACTIVE');
$legacyPage = null;

if (!$page instanceof Page || $page->isError()) {
    $legacyPage = Page::getByPath('/ministries', 'ACTIVE');
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
            'cName' => $churchLifeContent['name'],
            'cHandle' => 'community',
            'cDescription' => $churchLifeContent['description'],
        ],
        $fullTemplate
    );

    if ($legacyPage instanceof Page && !$legacyPage->isError()) {
        foreach ($legacyPage->getCollectionChildren('ACTIVE') as $childPage) {
            if ($childPage instanceof Page && !$childPage->isError()) {
                $childPage->move($page);
            }
        }

        $legacyPage->moveToTrash();
        $output->writeln('<info>Created /community and moved /ministries children across.</info>');
    } else {
        $output->writeln('<info>Created /community.</info>');
    }
}

$page->update([
    'cName' => $churchLifeContent['name'],
    'cHandle' => 'community',
    'cDescription' => $churchLifeContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $churchLifeContent['content']]);

$output->writeln('<info>Updated Church Life page content from the leadership questionnaire.</info>');

return 0;
