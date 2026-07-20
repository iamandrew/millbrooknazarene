<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Page;

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

$navigationData = require dirname(__FILE__) . '/navigation_builder.php';
$navigationGroups = $navigationData['groups'] ?? [];
$primaryMenuSections = $navigationData['primary_sections'] ?? [];
$navigationLinks = $navigationData['quick_links'] ?? [];

require_once dirname(__FILE__) . '/theme_assets.php';

$themePath = $view->getThemePath();
$preloadDefaultHero = millbrook_page_uses_default_hero($c ?? null);
$loadPlyrAssets = millbrook_page_uses_sermon_player($c ?? null);
$brandLogoUrl = millbrook_theme_asset_url($themePath, 'images/' . $brandLogo);
$brandLogoWidth = $wheelHeaderLogo ? '1080' : '2361';
$brandLogoHeight = $wheelHeaderLogo ? '1080' : '490';
$homeMetaDescription = 'A warm, family-friendly, Christ-centred church in Larne, living out faith in practical care for others.';
$pageMetaDescription = isset($c) && method_exists($c, 'getCollectionDescription') ? trim((string) $c->getCollectionDescription()) : '';
$thumbnailUrl = millbrook_page_thumbnail_url($c ?? null);
$ogImageUrl = millbrook_absolute_url($thumbnailUrl !== '' ? $thumbnailUrl : millbrook_default_hero_fallback_url($themePath));
?>
<!doctype html>
<html lang="<?php echo Localization::activeLanguage(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied'
        });
    </script>
    <?php Loader::element('header_required'); ?>
    <?php if ($isHomePage && $pageMetaDescription === '') { ?>
        <meta name="description" content="<?php echo h($homeMetaDescription); ?>">
    <?php } ?>
    <meta property="og:image" content="<?php echo h($ogImageUrl); ?>">
    <meta property="og:image:width" content="1800">
    <meta property="og:image:height" content="956">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&family=Sora:wght@300;400;500;600;700;800&display=swap">
    <?php if ($preloadDefaultHero) { ?>
        <link rel="preload" as="image" href="<?php echo h(millbrook_default_hero_image_url($themePath)); ?>" type="image/webp" fetchpriority="high">
    <?php } ?>
    <?php if ($loadPlyrAssets) { ?>
        <link rel="stylesheet" href="<?php echo h(millbrook_theme_asset_url($themePath, 'vendor/plyr/plyr.css')); ?>">
    <?php } ?>
    <link rel="stylesheet" href="<?php echo h(millbrook_theme_asset_url($themePath, 'css/main.css')); ?>">
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
                        echo '<a href="mailto:info@millbrooknazarene.church">info@millbrooknazarene.church</a>';
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
                        src="<?php echo h($brandLogoUrl); ?>"
                        alt="Millbrook Church of the Nazarene"
                        class="brand-logo<?php echo $wheelHeaderLogo ? ' brand-logo--wheel' : ''; ?>"
                        width="<?php echo $brandLogoWidth; ?>"
                        height="<?php echo $brandLogoHeight; ?>"
                    >
                </a>

                <div class="header-actions">
                    <?php
                    $headerActions = new GlobalArea('Header Actions');
                    if ($headerActions->getTotalBlocksInArea($c) > 0) {
                        $headerActions->display($c);
                    } else {
                        echo '<a href="/visit-us" class="button button--primary">Visit Us?</a>';
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
                    <?php if ($navigationLinks !== []) { ?>
                        <nav class="site-menu__quick" aria-label="Quick links">
                            <?php foreach ($navigationLinks as $index => $link) { ?>
                                <a
                                    href="<?php echo h($link['url']); ?>"
                                    target="<?php echo h($link['target']); ?>"
                                    class="site-menu__quick-link<?php echo $index === 0 ? ' site-menu__quick-link--primary' : ''; ?>"
                                >
                                    <?php echo h($link['name']); ?>
                                </a>
                            <?php } ?>
                        </nav>
                    <?php } ?>

                    <?php if ($primaryMenuSections !== []) { ?>
                        <div class="site-menu__primary site-menu__primary--journey">
                            <?php foreach ($primaryMenuSections as $section) { ?>
                                <section class="site-menu__card site-menu__card--<?php echo h($section['accent']); ?>">
                                    <a
                                        href="<?php echo h($section['url']); ?>"
                                        target="<?php echo h($section['target']); ?>"
                                        class="site-menu__card-heading<?php echo $section['is_current'] || $section['in_path'] ? ' is-active' : ''; ?>"
                                    >
                                        <span><?php echo h($section['heading']); ?></span>
                                        <span class="site-menu__arrow" aria-hidden="true">&rarr;</span>
                                    </a>

                                    <p class="site-menu__tag"><?php echo h($section['eyebrow']); ?></p>
                                    <p class="site-menu__description"><?php echo h($section['description']); ?></p>

                                    <ul class="site-menu__list">
                                        <?php foreach ($section['items'] as $item) { ?>
                                            <li class="site-menu__item">
                                                <a
                                                    href="<?php echo h($item['url']); ?>"
                                                    target="<?php echo h($item['target']); ?>"
                                                    class="site-menu__link<?php echo $item['is_current'] || $item['in_path'] ? ' is-active' : ''; ?>"
                                                >
                                                    <?php echo h($item['name']); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="site-menu__primary">
                            <?php foreach ($navigationGroups as $group) { ?>
                                <section class="site-menu__group">
                                    <a
                                        href="<?php echo h($group['heading']['url']); ?>"
                                        target="<?php echo h($group['heading']['target']); ?>"
                                        class="site-menu__heading<?php echo $group['heading']['is_current'] || $group['heading']['in_path'] ? ' is-active' : ''; ?>"
                                    >
                                        <?php echo h($group['heading']['name']); ?>
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
                        </div>
                    <?php } ?>

                    <div class="site-menu__footer">
                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Gatherings</p>
                            <p>Sundays at 11:00am</p>
                            <p>Homegroups on Sunday and Wednesday evenings</p>
                        </div>

                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Visit</p>
                            <p>Millbrook Community Centre</p>
                            <p>Drumahoe Road<br>Millbrook<br>BT40 2PF</p>
                        </div>

                        <div class="site-menu__meta">
                            <p class="site-menu__eyebrow">Contact</p>
                            <p><a href="mailto:info@millbrooknazarene.church">info@millbrooknazarene.church</a></p>
                            <p><a href="/contact">Get in touch</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
