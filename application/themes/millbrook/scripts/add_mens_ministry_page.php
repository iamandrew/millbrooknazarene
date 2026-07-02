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

$mensMinistryContent = require __DIR__ . '/content/mens_ministry.php';
$page = Page::getByPath('/community/mens-ministry', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $page = Page::getByPath('/community/men', 'ACTIVE');
}

if (!$page instanceof Page || $page->isError()) {
    $parent = Page::getByPath('/community', 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$parent instanceof Page || $parent->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve /community, page type, or full template.</error>');
        return 1;
    }

    $page = $parent->add(
        $pageType,
        [
            'cName' => $mensMinistryContent['name'],
            'cHandle' => 'mens-ministry',
            'cDescription' => $mensMinistryContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /community/mens-ministry.</info>');
}

$page->update([
    'cName' => $mensMinistryContent['name'],
    'cHandle' => 'mens-ministry',
    'cDescription' => $mensMinistryContent['description'],
]);
$page->rescanCollectionPath();
$page = Page::getByID($page->getCollectionID(), 'ACTIVE');

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $mensMinistryContent['content']]);

$output->writeln('<info>Updated Men\'s Ministry page content from the leadership questionnaire.</info>');

return 0;
