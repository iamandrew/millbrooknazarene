<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
?>

<main id="main-content" class="site-main">
    <?php
    if (isset($innerContent)) {
        echo $innerContent;
    } else {
        $this->inc('elements/hero.php');
        ?>
        <section class="page-content">
            <div class="container page-content__inner page-content__inner--prose">
                <?php
                $mainArea = new Area('Main');
                $mainArea->display($c);
                ?>
            </div>
        </section>
        <?php
    }
    ?>
</main>

<?php $this->inc('elements/footer.php'); ?>
