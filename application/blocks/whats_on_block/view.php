<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $title
 * @var string $intro
 * @var string $layout
 * @var array<int,array<string,string>> $items
 * @var bool $hasPrimaryButton
 * @var bool $hasSecondaryButton
 * @var string $primaryButtonLabel
 * @var string $primaryButtonUrl
 * @var string $secondaryButtonLabel
 * @var string $secondaryButtonUrl
 */
?>
<section class="whats-on-block whats-on-block--<?= h($layout) ?>">
    <?php if ($title !== '' || $intro !== '') { ?>
        <header class="whats-on-block__header">
            <?php if ($title !== '') { ?>
                <h2 class="whats-on-block__title"><?= h($title) ?></h2>
            <?php } ?>
            <?php if ($intro !== '') { ?>
                <p class="whats-on-block__intro"><?= h($intro) ?></p>
            <?php } ?>
        </header>
    <?php } ?>

    <?php if ($items) { ?>
        <div class="whats-on-block__items">
            <?php foreach ($items as $item) { ?>
                <article class="whats-on-block__item">
                    <?php if ($layout === 'compact') { ?>
                        <div class="whats-on-block__item-main">
                            <?php if ($item['eyebrow'] !== '') { ?>
                                <p class="whats-on-block__eyebrow"><?= h($item['eyebrow']) ?></p>
                            <?php } ?>
                            <div class="whats-on-block__item-copy">
                                <?php if ($item['title'] !== '') { ?>
                                    <h3 class="whats-on-block__item-title"><?= h($item['title']) ?></h3>
                                <?php } ?>
                                <?php if ($item['summary'] !== '') { ?>
                                    <p class="whats-on-block__summary"><?= h($item['summary']) ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($item['linkLabel'] !== '' && $item['linkUrl'] !== '') { ?>
                            <a class="text-link whats-on-block__item-link" href="<?= h($item['linkUrl']) ?>"><?= h($item['linkLabel']) ?></a>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($item['eyebrow'] !== '') { ?>
                            <p class="whats-on-block__eyebrow"><?= h($item['eyebrow']) ?></p>
                        <?php } ?>
                        <?php if ($item['title'] !== '') { ?>
                            <h3 class="whats-on-block__item-title"><?= h($item['title']) ?></h3>
                        <?php } ?>
                        <?php if ($item['summary'] !== '') { ?>
                            <p class="whats-on-block__summary"><?= h($item['summary']) ?></p>
                        <?php } ?>
                        <?php if ($item['linkLabel'] !== '' && $item['linkUrl'] !== '') { ?>
                            <a class="text-link" href="<?= h($item['linkUrl']) ?>"><?= h($item['linkLabel']) ?></a>
                        <?php } ?>
                    <?php } ?>
                </article>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if ($hasPrimaryButton || $hasSecondaryButton) { ?>
        <div class="whats-on-block__actions">
            <?php if ($hasPrimaryButton) { ?>
                <a class="button button--primary" href="<?= h($primaryButtonUrl) ?>"><?= h($primaryButtonLabel) ?></a>
            <?php } ?>
            <?php if ($hasSecondaryButton) { ?>
                <a class="button button--secondary" href="<?= h($secondaryButtonUrl) ?>"><?= h($secondaryButtonLabel) ?></a>
            <?php } ?>
        </div>
    <?php } ?>
</section>
