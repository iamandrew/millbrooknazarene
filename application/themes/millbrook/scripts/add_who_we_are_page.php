<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$page = Page::getByPath('/about/who-we-are', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /about/who-we-are.</error>');
    return 1;
}

$whoWeAreContent = require __DIR__ . '/content/who_we_are.php';

$page->update([
    'cName' => $whoWeAreContent['name'],
    'cDescription' => $whoWeAreContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $whoWeAreContent['content']]);

$output->writeln('<info>Updated Who We Are page content from the leadership questionnaire.</info>');

return 0;
