<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

$currentPageId = 0;
$trailIds = [];

if (isset($c) && $c instanceof Page && !$c->isError()) {
    $currentPageId = (int) $c->getCollectionID();

    $parent = $c;
    while ($parent instanceof Page && !$parent->isError()) {
        $trailIds[] = (int) $parent->getCollectionID();
        $parent = Page::getByID((int) $parent->getCollectionParentID(), 'ACTIVE');

        if ($parent instanceof Page && !$parent->isError() && (int) $parent->getCollectionID() === 1) {
            break;
        }
    }
}

$site = \Core::make('site')->getSite();
$rootPage = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');

$canIncludePage = static function (Page $page): bool {
    if ($page->isError()) {
        return false;
    }

    $permissionChecker = new Checker($page);
    return $permissionChecker->canViewPage();
};

$buildNavigationItem = static function (Page $page) use ($currentPageId, $trailIds): array {
    $label = trim((string) $page->getAttribute('nav_label'));
    if ($label === '') {
        $label = (string) $page->getCollectionName();
    }

    return [
        'id' => (int) $page->getCollectionID(),
        'name' => $label,
        'url' => (string) $page->getCollectionLink(),
        'target' => $page->isExternalLink() && $page->openCollectionPointerExternalLinkInNewWindow() ? '_blank' : '_self',
        'is_current' => (int) $page->getCollectionID() === $currentPageId,
        'in_path' => in_array((int) $page->getCollectionID(), $trailIds, true),
    ];
};

$navigationGroups = [];
$quickLinkPool = [];
$footerLinkPool = [];

if ($rootPage instanceof Page && !$rootPage->isError()) {
    foreach ($rootPage->getCollectionChildren('ACTIVE') as $topLevelPage) {
        if (!$topLevelPage instanceof Page || !$canIncludePage($topLevelPage)) {
            continue;
        }

        if ($topLevelPage->getAttribute('nav_show_in_menu')) {
            $heading = $buildNavigationItem($topLevelPage);
            $navigationGroups[] = [
                'eyebrow' => $heading['name'],
                'heading' => $heading,
                'children' => array_values(array_filter(array_map(
                    static function ($childPage) use ($canIncludePage, $buildNavigationItem) {
                        if (!$childPage instanceof Page || !$canIncludePage($childPage) || !$childPage->getAttribute('nav_show_in_menu')) {
                            return null;
                        }

                        return $buildNavigationItem($childPage);
                    },
                    $topLevelPage->getCollectionChildren('ACTIVE')
                ))),
            ];
        }

        $stack = [$topLevelPage];
        while ($stack !== []) {
            /** @var Page $page */
            $page = array_pop($stack);

            if ($canIncludePage($page) && $page->getAttribute('nav_show_in_quick_links')) {
                $quickLinkPool[] = [
                    'order' => (int) ($page->getAttribute('nav_quick_link_order') ?: 0),
                    'item' => $buildNavigationItem($page),
                ];
            }

            if ($canIncludePage($page) && $page->getAttribute('nav_show_in_footer')) {
                $footerLinkPool[] = [
                    'order' => (int) ($page->getAttribute('nav_footer_order') ?: 0),
                    'item' => $buildNavigationItem($page),
                ];
            }

            foreach ($page->getCollectionChildren('ACTIVE') as $childPage) {
                if ($childPage instanceof Page && !$childPage->isError()) {
                    $stack[] = $childPage;
                }
            }
        }
    }
}

usort(
    $quickLinkPool,
    static function (array $a, array $b): int {
        if ($a['order'] === $b['order']) {
            return strcmp($a['item']['name'], $b['item']['name']);
        }

        return $a['order'] <=> $b['order'];
    }
);

usort(
    $footerLinkPool,
    static function (array $a, array $b): int {
        if ($a['order'] === $b['order']) {
            return strcmp($a['item']['name'], $b['item']['name']);
        }

        return $a['order'] <=> $b['order'];
    }
);

return [
    'groups' => $navigationGroups,
    'quick_links' => array_column($quickLinkPool, 'item'),
    'footer_links' => array_column($footerLinkPool, 'item'),
];
