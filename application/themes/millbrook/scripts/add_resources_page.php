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

$resourcesContent = require __DIR__ . '/content/resources.php';
$page = Page::getByPath('/resources', 'ACTIVE');

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
            'cName' => $resourcesContent['name'],
            'cHandle' => 'resources',
            'cDescription' => $resourcesContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /resources.</info>');
}

$page->update([
    'cName' => $resourcesContent['name'],
    'cHandle' => 'resources',
    'cDescription' => $resourcesContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $resourcesContent['content']]);

$output->writeln('<info>Updated Resources page content.</info>');

return 0;
