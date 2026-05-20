<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/resources/policies', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /resources/policies.</error>');
    return 1;
}

$blockType = BlockType::getByHandle('google_drive_documents');
if (!$blockType) {
    $blockType = BlockType::installBlockType('google_drive_documents');
    $output->writeln('<info>Installed block type: google_drive_documents</info>');
}

if (!$blockType) {
    $output->writeln('<error>Could not install or load the Google Drive Documents block type.</error>');
    return 1;
}

$area = Area::getOrCreate($page, 'Main');
$existingBlocks = $area->getAreaBlocksArray($page);
$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$documentBlockData = [
    'title' => '',
    'intro' => '',
    'folderUrl' => '',
    'buttonLabel' => 'Open full folder',
    'showButton' => 1,
    'viewMode' => 'list',
    'embedHeight' => 620,
];

foreach ($existingBlocks as $block) {
    if ($block->getBlockTypeHandle() !== 'google_drive_documents') {
        continue;
    }

    $controller = $block->getController();
    $documentBlockData = [
        'title' => trim((string) ($controller->title ?? '')),
        'intro' => trim((string) ($controller->intro ?? '')),
        'folderUrl' => trim((string) ($controller->folderUrl ?? '')),
        'buttonLabel' => trim((string) ($controller->buttonLabel ?? '')) ?: 'Open full folder',
        'showButton' => !empty($controller->showButton) ? 1 : 0,
        'viewMode' => in_array((string) ($controller->viewMode ?? ''), ['list', 'grid'], true) ? (string) $controller->viewMode : 'list',
        'embedHeight' => max(360, min((int) ($controller->embedHeight ?? 620), 1200)),
    ];
    break;
}

foreach ($existingBlocks as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, [
    'content' => <<<'HTML'
<div class="content-intro">
  <p>You can use the document library below to access important church policies, safeguarding information, and other key documents.</p>
</div>
HTML,
]);

$page->addBlock($blockType, $area, [
    'title' => '',
    'intro' => '',
    'folderUrl' => $documentBlockData['folderUrl'],
    'buttonLabel' => $documentBlockData['buttonLabel'],
    'showButton' => $documentBlockData['showButton'],
    'viewMode' => $documentBlockData['viewMode'],
    'embedHeight' => $documentBlockData['embedHeight'],
]);

$output->writeln('<info>Rebuilt /resources/policies with a concise intro and document library block.</info>');

return 0;
