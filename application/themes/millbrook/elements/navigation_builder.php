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

    $url = (string) $page->getCollectionLink();
    if (!$page->isExternalLink()) {
        $path = (string) $page->getCollectionPath();
        if ($path !== '') {
            $url = $path === '/' ? '/' : $path;
        }
    }

    return [
        'id' => (int) $page->getCollectionID(),
        'name' => $label,
        'url' => $url,
        'target' => $page->isExternalLink() && $page->openCollectionPointerExternalLinkInNewWindow() ? '_blank' : '_self',
        'is_current' => (int) $page->getCollectionID() === $currentPageId,
        'in_path' => in_array((int) $page->getCollectionID(), $trailIds, true),
    ];
};

$primaryMenuSections = [
    'plan_visit' => [
        'handle' => 'plan_visit',
        'heading' => 'Plan a visit',
        'eyebrow' => 'Sunday',
        'description' => 'Times, parking, children, and what happens.',
        'accent' => 'lime',
    ],
    'connect' => [
        'handle' => 'connect',
        'heading' => 'Get connected',
        'eyebrow' => 'People',
        'description' => 'Groups and gatherings where friendship can grow.',
        'accent' => 'blue',
    ],
    'young_people' => [
        'handle' => 'young_people',
        'heading' => 'For young people',
        'eyebrow' => 'Children & youth',
        'description' => 'Places for babies, children, and secondary school age young people.',
        'accent' => 'purple',
    ],
    'faith_support' => [
        'handle' => 'faith_support',
        'heading' => 'Faith & support',
        'eyebrow' => 'Care',
        'description' => 'Teaching, prayer, beliefs, giving, and getting in touch.',
        'accent' => 'coral',
    ],
];

$fallbackMenuMap = [
    '/visit-us' => ['section' => 'plan_visit', 'order' => 10],
    '/community/whats-on' => ['section' => 'plan_visit', 'order' => 20],
    '/contact' => ['section' => 'plan_visit', 'order' => 30],
    '/about' => ['section' => 'connect', 'order' => 10],
    '/community' => ['section' => 'connect', 'order' => 20],
    '/community/homegroups' => ['section' => 'connect', 'order' => 30],
    '/community/young-adults' => ['section' => 'connect', 'order' => 40],
    '/community/mens-ministry' => ['section' => 'connect', 'order' => 50],
    '/community/womens-ministry' => ['section' => 'connect', 'order' => 60],
    '/community/creche' => ['section' => 'young_people', 'order' => 10],
    '/community/children' => ['section' => 'young_people', 'order' => 20],
    '/community/youth' => ['section' => 'young_people', 'order' => 30],
    '/resources/sermons' => ['section' => 'faith_support', 'order' => 10],
    '/about/what-we-believe' => ['section' => 'faith_support', 'order' => 20],
    '/giving' => ['section' => 'faith_support', 'order' => 30],
    '/resources/policies' => ['section' => 'faith_support', 'order' => 40],
];

$normaliseMenuSection = static function ($section) use ($primaryMenuSections): string {
    $section = strtolower(trim((string) $section));
    $section = preg_replace('/[^a-z0-9]+/', '_', $section);
    $section = trim((string) $section, '_');

    $aliases = [
        'plan' => 'plan_visit',
        'visit' => 'plan_visit',
        'plan_a_visit' => 'plan_visit',
        'sunday' => 'plan_visit',
        'get_connected' => 'connect',
        'connected' => 'connect',
        'belong' => 'connect',
        'people' => 'connect',
        'children_youth' => 'young_people',
        'children_and_youth' => 'young_people',
        'young_people' => 'young_people',
        'youth' => 'young_people',
        'faith' => 'faith_support',
        'faith_support' => 'faith_support',
        'support' => 'faith_support',
        'care' => 'faith_support',
    ];

    $section = $aliases[$section] ?? $section;

    return isset($primaryMenuSections[$section]) ? $section : '';
};

$navigationGroups = [];
$primaryMenuPool = [];
$quickLinkPool = [];
$footerLinkPool = [];

if ($rootPage instanceof Page && !$rootPage->isError()) {
    $pagesToInspect = $rootPage->getCollectionChildren('ACTIVE');

    foreach ($pagesToInspect as $topLevelPage) {
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

            $path = (string) $page->getCollectionPath();
            $fallbackMenuItem = $fallbackMenuMap[$path] ?? [];

            if ($canIncludePage($page) && ($page->getAttribute('nav_show_in_menu') || $fallbackMenuItem !== [])) {
                $section = $normaliseMenuSection($page->getAttribute('nav_menu_section'));
                if ($section === '' && isset($fallbackMenuItem['section'])) {
                    $section = (string) $fallbackMenuItem['section'];
                }

                if ($section !== '' && isset($primaryMenuSections[$section])) {
                    $primaryMenuPool[] = [
                        'section' => $section,
                        'order' => (int) ($page->getAttribute('nav_menu_order') ?: ($fallbackMenuItem['order'] ?? 0)),
                        'item' => $buildNavigationItem($page),
                    ];
                }
            }

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

$primaryMenu = [];

foreach ($primaryMenuSections as $handle => $section) {
    $sectionItems = array_values(array_filter(
        $primaryMenuPool,
        static function (array $entry) use ($handle): bool {
            return $entry['section'] === $handle;
        }
    ));

    usort(
        $sectionItems,
        static function (array $a, array $b): int {
            if ($a['order'] === $b['order']) {
                return strcmp($a['item']['name'], $b['item']['name']);
            }

            return $a['order'] <=> $b['order'];
        }
    );

    $items = array_column($sectionItems, 'item');
    if ($items === []) {
        continue;
    }

    $primaryItem = $items[0];
    $section['url'] = $primaryItem['url'];
    $section['target'] = $primaryItem['target'];
    $section['is_current'] = (bool) array_filter($items, static function (array $item): bool {
        return $item['is_current'];
    });
    $section['in_path'] = (bool) array_filter($items, static function (array $item): bool {
        return $item['in_path'];
    });
    $section['items'] = $items;
    $primaryMenu[] = $section;
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
    'primary_sections' => $primaryMenu,
    'quick_links' => array_column($quickLinkPool, 'item'),
    'footer_links' => array_column($footerLinkPool, 'item'),
];
