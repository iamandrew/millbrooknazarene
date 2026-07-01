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

$whatWeBelieveContent = require __DIR__ . '/content/what_we_believe.php';
$page = Page::getByPath('/about/what-we-believe', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $parent = Page::getByPath('/about', 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$parent instanceof Page || $parent->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve /about, page type, or full template.</error>');
        return 1;
    }

    $page = $parent->add(
        $pageType,
        [
            'cName' => $whatWeBelieveContent['name'],
            'cHandle' => 'what-we-believe',
            'cDescription' => $whatWeBelieveContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /about/what-we-believe.</info>');
}

$page->update([
    'cName' => $whatWeBelieveContent['name'],
    'cHandle' => 'what-we-believe',
    'cDescription' => $whatWeBelieveContent['description'],
]);

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $whatWeBelieveContent['content']]);

$output->writeln('<info>Updated What We Believe page content from the leadership questionnaire.</info>');

return 0;
