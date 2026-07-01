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

$page->addBlock($contentBlockType, $area, ['content' => $whatsOnContent['content']]);

$output->writeln('<info>Updated What\'s On page content from the leadership questionnaire.</info>');

return 0;
