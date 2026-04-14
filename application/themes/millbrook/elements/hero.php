<?php
defined('C5_EXECUTE') or die("Access Denied.");

require __DIR__ . '/page_hero_data.php';

$hero = new GlobalArea('Hero');
$hasCustomHero = $hero->getTotalBlocksInArea($c) > 0;
$heroImageStyle = $pageHeroImageUrl !== '' ? sprintf('--hero-image: url(\'%s\');', h($pageHeroImageUrl)) : '';
?>

<section
    class="page-hero<?php echo $hasCustomHero ? ' page-hero--custom' : ''; ?><?php echo $pageHeroImageUrl !== '' ? ' page-hero--has-image' : ''; ?>"
>
    <div class="container page-hero__layout">
        <?php if ($hasCustomHero) { ?>
            <?php $hero->display($c); ?>
        <?php } else { ?>
            <div class="page-hero__brand">
                <h1 class="page-hero__title"><?php echo h($pageTitle); ?></h1>
                <p class="page-hero__description">
                    <?php
                    echo h(
                        $pageDescription !== ''
                            ? $pageDescription
                            : 'Find information, resources, and next steps for church life at Millbrook Church of the Nazarene.'
                    );
                    ?>
                </p>
            </div>
            <?php if ($heroImageStyle !== '') { ?>
                <div class="page-hero__media">
                    <div class="hero-visual">
                        <div class="hero-image-card" aria-hidden="true" style="<?php echo $heroImageStyle; ?>"></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</section>
