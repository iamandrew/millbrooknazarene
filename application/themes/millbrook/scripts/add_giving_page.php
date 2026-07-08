<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$page = Page::getByPath('/giving', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /giving.</error>');
    return 1;
}

$givingContent = require __DIR__ . '/content/giving.php';

$page->update([
    'cName' => $givingContent['name'],
    'cDescription' => $givingContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $givingContent['content']]);

$output->writeln('<info>Updated Giving page content with the live Give A Little form.</info>');

return 0;
