<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
require __DIR__ . '/elements/page_hero_data.php';
$mainArea = new Area('Main');
$heroImageStyle = $pageHeroImageUrl !== '' ? sprintf('--hero-image: url(\'%s\');', h($pageHeroImageUrl)) : '';
?>

<main id="main-content" class="site-main">
    <section class="full-page-hero<?php echo $pageHeroImageUrl !== '' ? ' full-page-hero--has-image' : ''; ?>">
        <div class="container full-page-hero__layout">
            <div class="full-page-hero__brand">
                <h1 class="full-page-hero__title"><?php echo h($pageTitle); ?></h1>
                <?php if ($pageDescription !== '') { ?>
                    <p class="full-page-hero__description"><?php echo h($pageDescription); ?></p>
                <?php } ?>
            </div>
            <?php if ($heroImageStyle !== '') { ?>
                <div class="full-page-hero__media">
                    <div class="hero-visual">
                        <div class="hero-image-card" aria-hidden="true" style="<?php echo $heroImageStyle; ?>"></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="page-content page-content--full page-content--builder">
        <div class="page-content__inner page-content__inner--full page-content__inner--builder">
            <?php $mainArea->display($c); ?>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
