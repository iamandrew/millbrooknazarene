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
$crecheContent = require __DIR__ . '/content/creche.php';
$youthContent = require __DIR__ . '/content/youth.php';

$pageMap = [
    '/community/cheesy-nachos' => $youthContent,
    '/community/mens-ministry' => [
        'name' => 'Men',
        'description' => 'A space for men to grow in faith, friendship, and service together.',
        'content' => <<<'HTML'
<section class="content-section">
<h2>Men</h2>
<p>Gatherings for men create opportunities for connection, encouragement, and spiritual growth. Through conversation, prayer, and shared activities, we want men to be strengthened in faith and equipped for everyday discipleship.</p>

<h3>Why It Matters</h3>
<p>It can be difficult to build meaningful friendships and make time for spiritual growth in the middle of work, family life, and responsibility. This group creates room for honest conversation, prayer, and mutual encouragement.</p>
</section>
HTML,
    ],
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
