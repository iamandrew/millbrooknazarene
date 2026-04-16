<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

$collectionPath = '';
if (isset($c) && method_exists($c, 'getCollectionPath')) {
    $collectionPath = (string) $c->getCollectionPath();
}

$isHomePage = isset($c) && method_exists($c, 'isHomePage') && $c->isHomePage();
$overlayHeader = $isHomePage || $collectionPath === '/' || $collectionPath === '';
$heroHeader = true;
$wheelHeaderLogo = $heroHeader;
$brandLogo = $wheelHeaderLogo ? 'logo-wheel.svg' : 'logo-no-sub.svg';

$currentPageId = isset($c) && method_exists($c, 'getCollectionID') ? (int) $c->getCollectionID() : 0;
$trailIds = [];
$trailPage = $c ?? null;
while ($trailPage instanceof Page && !$trailPage->isError() && $trailPage->getCollectionID() > 0) {
    $trailIds[] = (int) $trailPage->getCollectionID();
    $parentId = (int) $trailPage->getCollectionParentID();
    if ($parentId <= 0 || $parentId === $trailPage->getCollectionID()) {
        break;
    }
    $trailPage = Page::getByID($parentId, 'ACTIVE');
}

$buildNavigationItems = static function (Page $parentPage, int $depth = 1) use (&$buildNavigationItems, $currentPageId, $trailIds): array {
    $items = [];

    foreach ($parentPage->getCollectionChildren('ACTIVE') as $page) {
        if (!$page instanceof Page || $page->isError()) {
            continue;
        }

        $permissionChecker = new Checker($page);
        if (!$permissionChecker->canViewPage() || (bool) $page->getAttribute('exclude_nav')) {
            continue;
        }

        $pageItem = new PageItem($page, $page->getCollectionID() === $currentPageId);
        $children = [];

        if (!(bool) $page->getAttribute('exclude_subpages_from_nav')) {
            $children = $buildNavigationItems($page, $depth + 1);
        }

        $items[] = [
            'id' => (int) $page->getCollectionID(),
            'name' => $pageItem->getName(),
            'url' => $pageItem->getURL(),
            'target' => $page->isExternalLink() && $page->openCollectionPointerExternalLinkInNewWindow() ? '_blank' : '_self',
            'is_current' => (int) $page->getCollectionID() === $currentPageId,
            'in_path' => in_array((int) $page->getCollectionID(), $trailIds, true),
            'children' => $children,
            'depth' => $depth,
        ];
    }

    return $items;
};

$homePage = isset($c) ? Page::getByID($c->getSiteHomePageID(), 'ACTIVE') : null;
$navigationItems = $homePage instanceof Page && !$homePage->isError()
    ? $buildNavigationItems($homePage)
    : [];

$navigationGroups = array_values(array_filter($navigationItems, static fn(array $item): bool => $item['children'] !== []));
$navigationLinks = array_values(array_filter($navigationItems, static fn(array $item): bool => $item['children'] === []));
?>
<!doctype html>
<html lang="<?php echo Localization::activeLanguage(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php Loader::element('header_required'); ?>
    <link rel="stylesheet" href="<?php echo $view->getThemePath(); ?>/css/main.css">
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<div class="<?php echo $c->getPageWrapperClass(); ?>">
    <header class="site-header<?php echo $heroHeader ? ' site-header--hero' : ''; ?><?php echo $overlayHeader ? ' site-header--overlay' : ''; ?>">
<!--
        <div class="site-utility">
            <div class="container site-utility__layout">
                <div class="site-utility__copy">
                    <?php
                    $topBarLeft = new GlobalArea('Top Bar Left');
                    if ($topBarLeft->getTotalBlocksInArea($c) > 0) {
                        $topBarLeft->display($c);
                    } else {
                        echo '<span>Sundays at 11:00am</span><span>Millbrook Community Centre</span>';
                    }
                    ?>
                </div>

                <div class="site-utility__links">
                    <?php
                    $topBarRight = new GlobalArea('Top Bar Right');
                    if ($topBarRight->getTotalBlocksInArea($c) > 0) {
                        $topBarRight->display($c);
                    } else {
                        echo '<a href="mailto:info@millbrooknazarene.co.uk">info@millbrooknazarene.co.uk</a>';
                        echo '<a href="/contact">Get in touch</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
-->

        <div class="header-shell">
            <div class="container header-shell__layout">
                <div class="header-shell__menu">
                    <button
                        class="menu-toggle"
                        type="button"
                        aria-expanded="false"
                        aria-controls="siteMenu"
                    >
                        <span class="menu-toggle__icon" aria-hidden="true"></span>
                        <span class="menu-toggle__label">Menu</span>
                    </button>
                </div>

                <a class="brand" href="/" aria-label="Millbrook Church home">
                    <img
                        src="<?php echo $view->getThemePath(); ?>/images/<?php echo h($brandLogo); ?>"
                        alt="Millbrook Church of the Nazarene"
                        class="brand-logo<?php echo $wheelHeaderLogo ? ' brand-logo--wheel' : ''; ?>"
                    >
                </a>

                <div class="header-actions">
                    <?php
                    $headerActions = new GlobalArea('Header Actions');
                    if ($headerActions->getTotalBlocksInArea($c) > 0) {
                        $headerActions->display($c);
                    } else {
                        echo '<a href="/#home-next-steps" class="button button--primary">Visit Us</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>

    <div id="siteMenu" class="site-menu" aria-hidden="true">
        <div class="site-menu__backdrop" data-menu-close></div>
        <div class="site-menu__panel" role="dialog" aria-modal="true" aria-label="Site menu">
            <div class="container site-menu__shell">
                <div class="site-menu__content">
                    <div class="site-menu__primary">
                        <?php foreach ($navigationGroups as $group) { ?>
                            <section class="site-menu__group">
                                <p class="site-menu__eyebrow"><?php echo h($group['name']); ?></p>
                                <a
                                    href="<?php echo h($group['url']); ?>"
                                    target="<?php echo h($group['target']); ?>"
                                    class="site-menu__heading<?php echo $group['is_current'] || $group['in_path'] ? ' is-active' : ''; ?>"
                                >
                                    <?php echo h($group['name']); ?>
                                </a>

                                <ul class="site-menu__list">
                                    <?php foreach ($group['children'] as $child) { ?>
                                        <li class="site-menu__item">
                                            <a
                                                href="<?php echo h($child['url']); ?>"
                                                target="<?php echo h($child['target']); ?>"
                                                class="site-menu__link<?php echo $child['is_current'] || $child['in_path'] ? ' is-active' : ''; ?>"
                                            >
                                                <?php echo h($child['name']); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>

                        <?php if ($navigationLinks !== []) { ?>
                            <section class="site-menu__group site-menu__group--links">
                                <p class="site-menu__eyebrow">Quick Links</p>
                                <ul class="site-menu__feature-list">
                                    <?php foreach ($navigationLinks as $link) { ?>
                                        <li class="site-menu__feature-item">
                                            <a
                                                href="<?php echo h($link['url']); ?>"
                                                target="<?php echo h($link['target']); ?>"
                                                class="site-menu__feature-link<?php echo $link['is_current'] || $link['in_path'] ? ' is-active' : ''; ?>"
                                            >
                                                <?php echo h($link['name']); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                    </div>

                    <div class="site-menu__footer">
                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Gatherings</p>
                            <p>Sundays at 11:00am</p>
                            <p>Home Group every Thursday, 7:30pm</p>
                        </div>

                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Visit</p>
                            <p>Millbrook Community Centre</p>
                            <p>Drumahoe Road<br>Millbrook<br>BT40 2PF</p>
                        </div>

                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Contact</p>
                            <p><a href="mailto:info@millbrooknazarene.co.uk">info@millbrooknazarene.co.uk</a></p>
                            <p><a href="/contact">Get in touch</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
