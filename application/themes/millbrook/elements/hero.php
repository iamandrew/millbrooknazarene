<?php
defined('C5_EXECUTE') or die("Access Denied.");

$hero = new GlobalArea('Hero');
$hasCustomHero = $hero->getTotalBlocksInArea($c) > 0;
$pageTitle = isset($c) && method_exists($c, 'getCollectionName') ? (string) $c->getCollectionName() : 'Millbrook Church';
$pageDescription = isset($c) && method_exists($c, 'getCollectionDescription') ? trim((string) $c->getCollectionDescription()) : '';
?>

<section class="page-hero<?php echo $hasCustomHero ? ' page-hero--custom' : ''; ?>">
    <div class="container">
        <?php if ($hasCustomHero) { ?>
            <?php $hero->display($c); ?>
        <?php } else { ?>
            <div class="page-hero__content">
                <p class="section-eyebrow">Millbrook Church</p>
                <h1><?php echo h($pageTitle); ?></h1>
                <p>
                    <?php
                    echo h(
                        $pageDescription !== ''
                            ? $pageDescription
                            : 'Find information, resources, and next steps for church life at Millbrook Church of the Nazarene.'
                    );
                    ?>
                </p>
            </div>
        <?php } ?>
    </div>
</section>
