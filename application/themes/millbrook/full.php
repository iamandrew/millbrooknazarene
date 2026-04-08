<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
$mainArea = new Area('Main');
$pageTitle = isset($c) && method_exists($c, 'getCollectionName') ? (string) $c->getCollectionName() : 'Millbrook Church';
$pageDescription = isset($c) && method_exists($c, 'getCollectionDescription') ? trim((string) $c->getCollectionDescription()) : '';
?>

<main id="main-content" class="site-main">
    <section class="full-page-hero">
        <div class="container full-page-hero__inner">
            <p class="full-page-hero__eyebrow">Millbrook Church</p>
            <h1><?php echo h($pageTitle); ?></h1>
            <?php if ($pageDescription !== '') { ?>
                <p class="full-page-hero__description"><?php echo h($pageDescription); ?></p>
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
