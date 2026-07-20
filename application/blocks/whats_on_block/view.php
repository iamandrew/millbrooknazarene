<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $title
 * @var string $intro
 * @var string $layout
 * @var array<int,string> $introParagraphs
 * @var array<int,array<string,string>> $items
 * @var array<string,array<int,array<string,string>>> $groupedItems
 * @var array<string,array<string,mixed>> $sectionConfig
 * @var bool $hasPrimaryButton
 * @var bool $hasSecondaryButton
 * @var string $primaryButtonLabel
 * @var string $primaryButtonUrl
 * @var string $secondaryButtonLabel
 * @var string $secondaryButtonUrl
 */
?>

<?php if ($layout === 'cards') { ?>
    <div class="content-guide content-guide--whats-on-grid">
        <section class="whats-on-intro">
            <div>
                <p class="content-kicker">What's On</p>
                <?php if ($title !== '') { ?>
                    <h2><?= h($title) ?></h2>
                <?php } ?>
                <?php foreach ($introParagraphs as $index => $paragraph) { ?>
                    <p<?= $index === 0 ? ' class="content-guide__lede"' : '' ?>><?= h($paragraph) ?></p>
                <?php } ?>
            </div>
            <aside class="whats-on-intro__note">
                <strong><?= t('Looking for a date?') ?></strong>
                <p><?= t('One-off events are normally advertised through church notices, Facebook, Instagram, and the weekly newsletter.') ?></p>
            </aside>
        </section>

        <?php if ($groupedItems) { ?>
            <?php foreach ($groupedItems as $section => $sectionItems) {
                $config = $sectionConfig[$section] ?? null;
                if (!$config || !$sectionItems) {
                    continue;
                }
                $cardsClass = trim((string) ($config['cardsClass'] ?? $section));
                ?>
                <section class="whats-on-board" aria-labelledby="whats-on-<?= h($section) ?>">
                    <div class="whats-on-board__header">
                        <div>
                            <p class="content-kicker"><?= h($config['kicker']) ?></p>
                            <h2 id="whats-on-<?= h($section) ?>"><?= h($config['title']) ?></h2>
                        </div>
                        <?php if (!empty($config['summary'])) { ?>
                            <p><?= h($config['summary']) ?></p>
                        <?php } ?>
                    </div>

                    <div class="whats-on-cards whats-on-cards--<?= h($cardsClass) ?>">
                        <?php foreach ($sectionItems as $item) {
                            $cardStyle = trim((string) ($item['cardStyle'] ?? 'blue'));
                            $hasLink = trim((string) ($item['linkUrl'] ?? '')) !== '';
                            $tag = $hasLink ? 'a' : 'article';
                            $href = $hasLink ? ' href="' . h($item['linkUrl']) . '"' : '';
                            ?>
                            <<?= $tag ?> class="whats-on-card whats-on-card--<?= h($cardStyle) ?>"<?= $href ?>>
                                <div class="whats-on-card__body">
                                    <?php if ($item['eyebrow'] !== '') { ?>
                                        <span class="whats-on-card__eyebrow"><?= h($item['eyebrow']) ?></span>
                                    <?php } ?>
                                    <?php if ($item['title'] !== '') { ?>
                                        <h3><?= h($item['title']) ?></h3>
                                    <?php } ?>
                                    <?php if ($item['summary'] !== '') { ?>
                                        <p><?= h($item['summary']) ?></p>
                                    <?php } ?>
                                    <?php if (!empty($item['details'])) { ?>
                                        <dl class="whats-on-card__details">
                                            <?php foreach ($item['details'] as $detail) { ?>
                                                <div>
                                                    <dt><?= h($detail['label']) ?></dt>
                                                    <dd><?= h($detail['value']) ?></dd>
                                                </div>
                                            <?php } ?>
                                        </dl>
                                    <?php } ?>
                                    <?php if ($item['meta'] !== '') { ?>
                                        <span class="whats-on-card__meta"><?= h($item['meta']) ?></span>
                                    <?php } ?>
                                    <?php if ($hasLink && $item['linkLabel'] !== '') { ?>
                                        <span class="whats-on-card__action"><?= h($item['linkLabel']) ?></span>
                                    <?php } ?>
                                </div>
                            </<?= $tag ?>>
                        <?php } ?>
                    </div>
                </section>
            <?php } ?>
        <?php } else { ?>
            <section class="whats-on-board">
                <div class="whats-on-board__header">
                    <div>
                        <p class="content-kicker"><?= t('Nothing listed') ?></p>
                        <h2><?= t('No current items to show.') ?></h2>
                    </div>
                    <p><?= t('Please check back soon, or contact us if you are looking for a particular gathering.') ?></p>
                </div>
            </section>
        <?php } ?>

        <section class="whats-on-help">
            <div>
                <p class="content-kicker">New or unsure?</p>
                <h2>We can help you find the right place to start.</h2>
                <p>It is natural to feel unsure about walking into something new. If you message us before coming, we can let you know what to expect and help you find a friendly face.</p>
            </div>
            <div class="whats-on-help__links">
                <p><strong>Check this week’s details</strong><br><a href="mailto:info@millbrooknazarene.church">info@millbrooknazarene.church</a></p>
                <p><strong>Coming on Sunday?</strong><br><a href="/visit-us">Plan your first visit</a></p>
                <p><strong>Want the wider picture?</strong><br><a href="/community">Explore Church Life</a></p>
            </div>
        </section>
    </div>
<?php } else { ?>
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
<?php } ?>
