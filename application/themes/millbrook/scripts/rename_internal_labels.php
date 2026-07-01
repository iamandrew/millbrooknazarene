<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$replaceMainArea = static function (Page $page, string $html) use ($contentBlockType, $output): void {
    $area = Area::getOrCreate($page, 'Main');
    foreach ($area->getAreaBlocksArray($page) as $block) {
        $block->deleteBlock();
    }

    $page->addBlock($contentBlockType, $area, ['content' => $html]);
    $output->writeln(sprintf('Updated content: %s', $page->getCollectionPath()));
};

$womensMinistryContent = require __DIR__ . '/content/womens_ministry.php';
$mensMinistryContent = require __DIR__ . '/content/mens_ministry.php';
$crecheContent = require __DIR__ . '/content/creche.php';
$youthContent = require __DIR__ . '/content/youth.php';

$pageMap = [
    '/community/cheesy-nachos' => $youthContent,
    '/community/mens-ministry' => $mensMinistryContent,
    '/community/womens-ministry' => $womensMinistryContent,
    '/community/creche' => $crecheContent,
];

foreach ($pageMap as $path => $data) {
    $page = Page::getByPath($path, 'ACTIVE');
    if (!$page instanceof Page || $page->isError()) {
        $output->writeln(sprintf('<comment>Skipped missing page: %s</comment>', $path));
        continue;
    }

    $page->update([
        'cName' => $data['name'],
        'cDescription' => $data['description'],
    ]);

    $replaceMainArea($page, $data['content']);
}

$output->writeln('<info>Renamed internal labels to public-facing page names.</info>');

return 0;
