<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/resources/sermons', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /resources/sermons.</error>');
    return 1;
}

$desiredDescription = 'Listen back to recent sermons and Bible teaching from Millbrook.';
$legacyDescriptions = [
    '',
    'Recent teaching and sermon series from Millbrook Church.',
];

if (in_array(trim((string) $page->getCollectionDescription()), $legacyDescriptions, true)) {
    $page->update([
        'cDescription' => $desiredDescription,
    ]);
    $page = Page::getByID($page->getCollectionID(), 'ACTIVE');
}

$blockType = BlockType::getByHandle('latest_sermons');
if (!$blockType) {
    $blockType = BlockType::installBlockType('latest_sermons');
    $output->writeln('<info>Installed block type: latest_sermons</info>');
}

if (!$blockType) {
    $output->writeln('<error>Could not install or load the Latest Sermons block type.</error>');
    return 1;
}

$area = Area::getOrCreate($page, 'Main');
$existingBlocks = $area->getAreaBlocksArray($page);
$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$sermonsBlockData = [
    'title' => '',
    'intro' => '',
    'sourceType' => 'concrete_uploads',
    'displayLimit' => 12,
    'showPlayer' => 1,
    'showArchiveButton' => 0,
    'archiveButtonLabel' => 'Latest Sermons',
    'archiveButtonUrl' => '/resources/sermons',
];

foreach ($existingBlocks as $block) {
    if ($block->getBlockTypeHandle() !== 'latest_sermons') {
        continue;
    }

    $controller = $block->getController();
    $sermonsBlockData = [
        'title' => trim((string) ($controller->title ?? '')),
        'intro' => trim((string) ($controller->intro ?? '')),
        'sourceType' => in_array((string) ($controller->sourceType ?? ''), ['concrete_uploads', 'spotify'], true) ? (string) $controller->sourceType : 'concrete_uploads',
        'displayLimit' => max(1, min((int) ($controller->displayLimit ?? 12), 24)),
        'showPlayer' => !empty($controller->showPlayer) ? 1 : 0,
        'showArchiveButton' => !empty($controller->showArchiveButton) ? 1 : 0,
        'archiveButtonLabel' => trim((string) ($controller->archiveButtonLabel ?? '')) ?: 'Latest Sermons',
        'archiveButtonUrl' => trim((string) ($controller->archiveButtonUrl ?? '')) ?: '/resources/sermons',
    ];
    break;
}

foreach ($existingBlocks as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, [
    'content' => <<<'HTML'
<div class="content-intro">
  <p>Listen back to recent sermons and Bible teaching from Millbrook.</p>
</div>
HTML,
]);

$page->addBlock($blockType, $area, $sermonsBlockData);

$output->writeln('<info>Rebuilt /resources/sermons with a concise intro and latest sermons block.</info>');

return 0;
