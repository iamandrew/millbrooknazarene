<?php

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$kidsClubContent = require __DIR__ . '/content/kids_club_2026.php';
$kidsClubTemplate = PageTemplate::getByHandle('kids_club_2026');
if (!$kidsClubTemplate) {
    $kidsClubTemplate = PageTemplate::add('kids_club_2026', 'Kids Club 2026');
    $output->writeln('<info>Created Kids Club 2026 page template.</info>');
}

$page = Page::getByPath('/kids-club-2026', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    if (!$root instanceof Page || $root->isError() || !$pageType) {
        $output->writeln('<error>Could not resolve the site home page or page type.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => $kidsClubContent['name'],
            'cHandle' => 'kids-club-2026',
            'cDescription' => $kidsClubContent['description'],
        ],
        $kidsClubTemplate
    );

    $output->writeln('<info>Created /kids-club-2026.</info>');
}

$page->update([
    'cName' => $kidsClubContent['name'],
    'cHandle' => 'kids-club-2026',
    'cDescription' => $kidsClubContent['description'],
    'pTemplateID' => $kidsClubTemplate->getPageTemplateID(),
]);

$disableHeroImageKey = CollectionKey::getByHandle('disable_hero_image');
if ($disableHeroImageKey) {
    $page->setAttribute($disableHeroImageKey, true);
}

$output->writeln('<info>Updated Kids Club 2026 page template and registration form.</info>');

return 0;
