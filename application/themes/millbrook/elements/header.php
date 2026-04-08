<?php
defined('C5_EXECUTE') or die("Access Denied.");

$collectionPath = '';
if (isset($c) && method_exists($c, 'getCollectionPath')) {
    $collectionPath = (string) $c->getCollectionPath();
}

$isHomePage = isset($c) && method_exists($c, 'isHomePage') && $c->isHomePage();
$transparentHeader = $isHomePage || $collectionPath === '/' || $collectionPath === '';

$renderFallbackNav = static function (string $className = 'nav-list'): void {
    ?>
    <ul class="<?php echo h($className); ?>">
        <li><a href="/#home-vision">Vision</a></li>
        <li><a href="/resources/sermons">Teachings</a></li>
        <li><a href="/#home-next-steps">Visit</a></li>
        <li><a href="/giving">Give</a></li>
        <li><a href="/contact">Contact</a></li>
    </ul>
    <?php
};
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
    <header class="site-header<?php echo $transparentHeader ? ' transparent' : ''; ?><?php echo $isHomePage ? ' site-header--home' : ''; ?>">
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
                <a class="brand" href="/" aria-label="Millbrook Church home">
                    <img
                        src="<?php echo $view->getThemePath(); ?>/images/logo.png"
                        alt="Millbrook Church of the Nazarene"
                        class="brand-logo"
                    >
                </a>

                <nav class="site-nav" aria-label="Main navigation">
                    <?php
                    $navigation = new GlobalArea('Header Navigation');
                    if ($navigation->getTotalBlocksInArea($c) > 0) {
                        $navigation->display($c);
                    } else {
                        $renderFallbackNav();
                    }
                    ?>
                </nav>

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

                <button
                    class="mobile-nav-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="mobileNav"
                >
                    <span>Menu</span>
                </button>
            </div>

            <div id="mobileNav" class="mobile-nav" aria-label="Mobile navigation">
                <div class="container mobile-nav__inner">
                    <?php
                    $mobileNavigation = new GlobalArea('Header Navigation Mobile');
                    if ($mobileNavigation->getTotalBlocksInArea($c) > 0) {
                        $mobileNavigation->display($c);
                    } else {
                        $renderFallbackNav('mobile-nav-list');
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>
