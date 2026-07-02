<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$blockType = BlockType::getByHandle('whats_on_block');
if (!$blockType) {
    $blockType = BlockType::installBlockType('whats_on_block');
    $output->writeln('<info>Installed block type: whats_on_block</info>');
}

if (!$blockType) {
    $output->writeln('<error>Could not install or load the What’s On block type.</error>');
    return 1;
}

$whatsOnContent = require __DIR__ . '/content/whats_on.php';
$page = Page::getByPath('/community/whats-on', 'ACTIVE');

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
            'cName' => $whatsOnContent['name'],
            'cHandle' => 'whats-on',
            'cDescription' => $whatsOnContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /community/whats-on.</info>');
}

$page->update([
    'cName' => $whatsOnContent['name'],
    'cHandle' => 'whats-on',
    'cDescription' => $whatsOnContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($blockType, $area, [
    'title' => $whatsOnContent['title'],
    'intro' => $whatsOnContent['intro'],
    'layout' => 'cards',
    'itemsJson' => '',
    'primaryButtonLabel' => '',
    'primaryButtonUrl' => '',
    'secondaryButtonLabel' => '',
    'secondaryButtonUrl' => '',
]);

$output->writeln('<info>Updated What\'s On page with the Express-driven What’s On block.</info>');

return 0;
