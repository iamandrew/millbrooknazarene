<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$page = Page::getByPath('/community/creche', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /community/creche.</error>');
    return 1;
}

$crecheContent = require __DIR__ . '/content/creche.php';

$page->update([
    'cName' => $crecheContent['name'],
    'cDescription' => $crecheContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $crecheContent['content']]);

$output->writeln('<info>Updated Creche page content from the leadership questionnaire.</info>');

return 0;
