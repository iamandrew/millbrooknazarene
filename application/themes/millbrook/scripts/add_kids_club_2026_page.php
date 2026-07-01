<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$kidsClubContent = require __DIR__ . '/content/kids_club_2026.php';
$page = Page::getByPath('/kids-club-2026', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$root instanceof Page || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => $kidsClubContent['name'],
            'cHandle' => 'kids-club-2026',
            'cDescription' => $kidsClubContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /kids-club-2026.</info>');
}

$page->update([
    'cName' => $kidsClubContent['name'],
    'cHandle' => 'kids-club-2026',
    'cDescription' => $kidsClubContent['description'],
]);

$disableHeroImageKey = CollectionKey::getByHandle('disable_hero_image');
if ($disableHeroImageKey) {
    $page->setAttribute($disableHeroImageKey, true);
}

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $kidsClubContent['content']]);

$output->writeln('<info>Updated Kids Club 2026 page content and registration form.</info>');

return 0;
