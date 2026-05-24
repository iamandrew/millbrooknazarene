<?php defined('C5_EXECUTE') or die("Access Denied.");

$homeUrl = DIR_REL . '/';
$mainArea = new Area('Main');
?>

<section
    class="not-found-page"
    aria-labelledby="not-found-title"
>
    <div class="not-found-page__hero">
        <div class="container not-found-page__layout">
            <div class="not-found-page__content">
                <p class="not-found-page__code">404 / Page Not Found</p>
                <h1 id="not-found-title">This page is not here.</h1>
                <p class="not-found-page__lead">
                    The link may have changed or the page may have moved. Start again from the home page,
                    plan a visit, or send us a note if you were looking for something specific.
                </p>

                <div class="not-found-page__actions" aria-label="Helpful links">
                    <a class="button button--primary" href="<?php echo h($homeUrl); ?>">Back to Home</a>
                    <a class="button button--ghost-light" href="<?php echo h(DIR_REL); ?>/visit-us">Visit Us?</a>
                    <a class="button button--ghost-light" href="<?php echo h(DIR_REL); ?>/contact">Contact</a>
                </div>
            </div>

            <aside class="not-found-page__detail" aria-label="Sunday worship details">
                <span>Sunday Worship</span>
                <strong>11:00am</strong>
                <p>Millbrook Community Centre, Larne</p>
            </aside>
        </div>
    </div>

    <div class="not-found-page__wayfinder">
        <div class="container not-found-page__wayfinder-layout">
            <p>Useful places to try next</p>
            <nav class="not-found-page__links" aria-label="Popular pages">
                <a class="not-found-page__link" href="<?php echo h(DIR_REL); ?>/visit-us">
                    <strong>Visit Us?</strong>
                    <span>Plan your first Sunday</span>
                </a>
                <a class="not-found-page__link" href="<?php echo h(DIR_REL); ?>/community/whats-on">
                    <strong>What's On</strong>
                    <span>Gatherings and groups</span>
                </a>
                <a class="not-found-page__link" href="<?php echo h(DIR_REL); ?>/resources/sermons">
                    <strong>Latest Sermons</strong>
                    <span>Recent Bible teaching</span>
                </a>
            </nav>
        </div>
    </div>

    <?php if ($mainArea->getTotalBlocksInArea($c) > 0) { ?>
        <div class="container not-found-page__custom-content">
            <?php $mainArea->display($c); ?>
        </div>
    <?php } ?>
</section>
