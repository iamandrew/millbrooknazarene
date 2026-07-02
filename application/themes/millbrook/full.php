<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
require __DIR__ . '/elements/page_hero_data.php';
$mainArea = new Area('Main');
$heroImageMarkup = $pageHeroImageUrl !== ''
    ? millbrook_hero_image_markup($pageHeroImageUrl, (bool) ($pageHeroImageIsDefault ?? false), $this->getThemePath())
    : '';
$collectionPath = isset($c) && method_exists($c, 'getCollectionPath') ? (string) $c->getCollectionPath() : '';
$isKidsClubPage = $collectionPath === '/kids-club-2026';
?>

<main id="main-content" class="site-main">
    <?php if (!$isKidsClubPage) { ?>
        <section class="full-page-hero<?php echo $pageHeroImageUrl !== '' ? ' full-page-hero--has-image' : ''; ?>">
            <div class="container full-page-hero__layout">
                <div class="full-page-hero__brand">
                    <h1 class="full-page-hero__title"><?php echo h($pageTitle); ?></h1>
                    <?php if ($pageDescription !== '') { ?>
                        <p class="full-page-hero__description"><?php echo h($pageDescription); ?></p>
                    <?php } ?>
                </div>
                <?php if ($heroImageMarkup !== '') { ?>
                    <div class="full-page-hero__media">
                        <div class="hero-visual">
                            <div class="hero-image-card" aria-hidden="true"><?php echo $heroImageMarkup; ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <section class="page-content page-content--full page-content--builder<?php echo $isKidsClubPage ? ' page-content--kids-club' : ''; ?>">
        <div class="page-content__inner page-content__inner--full page-content__inner--builder">
            <?php $mainArea->display($c); ?>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
