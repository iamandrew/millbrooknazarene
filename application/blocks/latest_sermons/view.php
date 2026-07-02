<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $title
 * @var string $intro
 * @var string $sourceType
 * @var array<int,array<string,mixed>> $sermons
 * @var string $emptyMessage
 * @var bool $showDescriptions
 * @var bool $showPlayer
 * @var bool $showArchiveButton
 * @var string $archiveButtonLabel
 * @var string $archiveButtonUrl
 * @var bool $showApplePodcastButton
 * @var string $applePodcastButtonLabel
 * @var string $applePodcastButtonUrl
 */
$featuredSermon = $sermons[0] ?? null;
$archiveSermons = $sermons;

$formatDomIdSuffix = static function ($value): string {
    $suffix = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $value) ?: '';
    $suffix = trim($suffix, '-_');

    return $suffix !== '' ? $suffix : substr(sha1((string) $value), 0, 12);
};

$featuredId = $featuredSermon ? (string) ($featuredSermon['id'] ?? '') : '';
$featuredIdSuffix = $featuredSermon ? $formatDomIdSuffix($featuredId) : '';
$playerId = $featuredSermon ? 'sermons-player-' . $featuredIdSuffix : '';
$featuredTitleId = $featuredSermon ? 'sermons-feature-title-' . $featuredIdSuffix : '';
$featuredMetaId = $featuredSermon ? 'sermons-feature-meta-' . $featuredIdSuffix : '';
$featuredDescriptionId = $featuredSermon ? 'sermons-feature-description-' . $featuredIdSuffix : '';
$featuredImageId = $featuredSermon ? 'sermons-feature-image-' . $featuredIdSuffix : '';
$featuredEyebrowId = $featuredSermon ? 'sermons-feature-eyebrow-' . $featuredIdSuffix : '';

$formatSermonMeta = static function (array $sermon): string {
    $parts = [];

    if (!empty($sermon['date_label'])) {
        $parts[] = $sermon['date_label'];
    }

    if (!empty($sermon['speaker'])) {
        $parts[] = $sermon['speaker'];
    }

    if (!empty($sermon['duration_label'])) {
        $parts[] = $sermon['duration_label'];
    }

    return implode(' · ', $parts);
};

$getDescriptionHtml = static function (array $sermon): string {
    $html = trim((string) ($sermon['description_html'] ?? ''));
    if ($html !== '') {
        return $html;
    }

    $text = trim((string) ($sermon['description'] ?? ''));

    return $text !== '' ? nl2br(h($text), false) : '';
};

$isSpotifyUrl = static function (string $url): bool {
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));

    return in_array($host, ['open.spotify.com', 'play.spotify.com'], true);
};

$archiveLinkClass = 'sermons-block__archive-link';
if ($isSpotifyUrl($archiveButtonUrl)) {
    $archiveLinkClass .= ' sermons-block__archive-link--spotify';
}

$applePodcastLinkClass = 'sermons-block__archive-link sermons-block__archive-link--apple';
$spotifyButtonDisplayLabel = preg_replace('/^\s*Listen\s+on\s+/i', '', $archiveButtonLabel) ?: $archiveButtonLabel;
$applePodcastButtonDisplayLabel = preg_replace('/^\s*Listen\s+on\s+/i', '', $applePodcastButtonLabel) ?: $applePodcastButtonLabel;
?>
<section class="sermons-block">
    <?php if ($title !== '' || $intro !== '') { ?>
        <header class="sermons-block__header">
            <?php if ($title !== '') { ?>
                <h2 class="sermons-block__title"><?= h($title) ?></h2>
            <?php } ?>
            <?php if ($intro !== '') { ?>
                <p class="sermons-block__intro"><?= h($intro) ?></p>
            <?php } ?>
        </header>
    <?php } ?>

    <div class="sermons-block__library">
        <div class="sermons-block__toolbar">
            <div class="sermons-block__toolbar-copy">
                <p class="sermons-block__count">
                    <?= count($sermons) === 1 ? t('1 sermon available') : t('%s sermons available', count($sermons)) ?>
                </p>
            </div>

            <?php if (($showArchiveButton && $archiveButtonUrl !== '') || $showApplePodcastButton) { ?>
                <div class="sermons-block__toolbar-actions">
                    <?php if ($showApplePodcastButton && $applePodcastButtonUrl !== '') { ?>
                        <a target="_blank" rel="noopener noreferrer" class="<?= h($applePodcastLinkClass) ?>" href="<?= h($applePodcastButtonUrl) ?>">
                            <span class="sermons-block__apple-icon" aria-hidden="true"></span>
                            <span class="sermons-block__apple-copy">
                                <span class="sermons-block__apple-prefix"><?= t('Listen on') ?></span>
                                <span class="sermons-block__apple-label"><?= h($applePodcastButtonDisplayLabel) ?></span>
                            </span>
                        </a>
                    <?php } ?>

                    <?php if ($showArchiveButton && $archiveButtonUrl !== '') { ?>
                        <a target="_blank" rel="noopener noreferrer" class="<?= h($archiveLinkClass) ?>" href="<?= h($archiveButtonUrl) ?>">
                            <?php if ($isSpotifyUrl($archiveButtonUrl)) { ?>
                                <span class="sermons-block__spotify-icon" aria-hidden="true"></span>
                                <span class="sermons-block__spotify-copy">
                                    <span class="sermons-block__spotify-prefix"><?= t('Listen on') ?></span>
                                    <span class="sermons-block__spotify-label"><?= h($spotifyButtonDisplayLabel) ?></span>
                                </span>
                            <?php } else { ?>
                                <?= h($archiveButtonLabel) ?>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php if ($sermons && $featuredSermon) { ?>
            <?php $featuredMeta = $formatSermonMeta($featuredSermon); ?>
            <?php $featuredDescriptionText = trim((string) ($featuredSermon['description'] ?? '')); ?>
            <?php $featuredDescriptionHtml = $getDescriptionHtml($featuredSermon); ?>
            <?php $featuredImageUrl = trim((string) ($featuredSermon['image_url'] ?? '')); ?>
            <article
                class="sermons-block__feature"
                data-featured-sermon
                data-sermon-id="<?= h((string) $featuredSermon['id']) ?>"
                data-sermon-title="<?= h($featuredSermon['title']) ?>"
                data-sermon-meta="<?= h($featuredMeta) ?>"
                data-sermon-description="<?= h($featuredDescriptionText) ?>"
                data-sermon-description-html="<?= h($featuredDescriptionHtml) ?>"
                data-sermon-stream="<?= h($featuredSermon['stream_url']) ?>"
                data-sermon-image="<?= h($featuredImageUrl) ?>"
            >
                <div class="sermons-block__feature-main">
                    <div class="sermons-block__feature-copy">
                        <p id="<?= h($featuredEyebrowId) ?>" class="sermons-block__feature-eyebrow"><?= t('Latest sermon') ?></p>
                        <h3 id="<?= h($featuredTitleId) ?>" class="sermons-block__feature-title"><?= h($featuredSermon['title']) ?></h3>
                        <?php if ($featuredMeta !== '') { ?>
                            <p id="<?= h($featuredMetaId) ?>" class="sermons-block__feature-meta"><?= h($featuredMeta) ?></p>
                        <?php } else { ?>
                            <p id="<?= h($featuredMetaId) ?>" class="sermons-block__feature-meta"></p>
                        <?php } ?>
                        <?php if ($showDescriptions) { ?>
                            <div
                                id="<?= h($featuredDescriptionId) ?>"
                                class="sermons-block__description"
                                data-sermon-description-target
                                <?= $featuredDescriptionHtml === '' ? 'hidden' : '' ?>
                            ><?= $featuredDescriptionHtml ?></div>
                        <?php } ?>
                    </div>

                    <figure
                        id="<?= h($featuredImageId) ?>"
                        class="sermons-block__feature-artwork"
                        data-sermon-image-target
                        <?= $featuredImageUrl === '' ? 'hidden' : '' ?>
                    >
                        <img
                            class="sermons-block__feature-image"
                            data-sermon-image-img
                            <?= $featuredImageUrl !== '' ? 'src="' . h($featuredImageUrl) . '"' : '' ?>
                            alt="<?= h(t('Artwork for %s', $featuredSermon['title'])) ?>"
                            loading="eager"
                            decoding="async"
                        >
                    </figure>
                </div>

                <?php if ($showPlayer) { ?>
                    <div class="sermons-block__player-wrap sermons-block__player-wrap--feature">
                        <audio
                            id="<?= h($playerId) ?>"
                            class="sermons-block__player js-sermon-player"
                            controls
                            controlsList="nodownload"
                            preload="none"
                            src="<?= h($featuredSermon['stream_url']) ?>"
                        >
                            <?= t('Your browser does not support audio playback.') ?>
                        </audio>
                    </div>
                <?php } ?>

                <?php if (!$showPlayer) { ?>
                    <div class="sermons-block__actions">
                        <a class="button button--primary" href="<?= h($featuredSermon['stream_url']) ?>">
                            <?= t('Listen now') ?>
                        </a>
                    </div>
                <?php } ?>
            </article>

            <?php if ($archiveSermons) { ?>
                <div class="sermons-block__archive">
                    <div class="sermons-block__archive-heading">
                        <p class="sermons-block__archive-eyebrow"><?= t('More sermons') ?></p>
                    </div>

                    <div class="sermons-block__archive-list">
                        <?php foreach ($archiveSermons as $sermon) { ?>
                            <?php $archiveMeta = $formatSermonMeta($sermon); ?>
                            <?php $sermonId = (string) ($sermon['id'] ?? ''); ?>
                            <?php $archiveDescriptionText = trim((string) ($sermon['description'] ?? '')); ?>
                            <?php $archiveDescriptionHtml = $getDescriptionHtml($sermon); ?>
                            <?php $archiveImageUrl = trim((string) ($sermon['image_url'] ?? '')); ?>
                            <article
                                class="sermons-block__archive-item<?= $featuredSermon && (string) $featuredSermon['id'] === $sermonId ? ' is-active' : '' ?>"
                                data-sermon-item
                                data-sermon-id="<?= h($sermonId) ?>"
                                data-sermon-title="<?= h($sermon['title']) ?>"
                                data-sermon-meta="<?= h($archiveMeta) ?>"
                                data-sermon-description="<?= h($archiveDescriptionText) ?>"
                                data-sermon-description-html="<?= h($archiveDescriptionHtml) ?>"
                                data-sermon-stream="<?= h($sermon['stream_url']) ?>"
                                data-sermon-image="<?= h($archiveImageUrl) ?>"
                            >
                                <?php if ($archiveImageUrl !== '') { ?>
                                    <img
                                        class="sermons-block__archive-image"
                                        src="<?= h($archiveImageUrl) ?>"
                                        alt="<?= h(t('Artwork for %s', $sermon['title'])) ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                <?php } ?>
                                <div class="sermons-block__archive-copy">
                                    <h4 class="sermons-block__archive-title" data-sermon-item-title>
                                        <a
                                            class="sermons-block__archive-title-link"
                                            href="<?= h($sermon['stream_url']) ?>"
                                            data-sermon-load="<?= h($playerId) ?>"
                                            data-sermon-title="<?= h($sermon['title']) ?>"
                                            data-sermon-meta="<?= h($archiveMeta) ?>"
                                            data-sermon-description="<?= h($archiveDescriptionText) ?>"
                                            data-sermon-description-html="<?= h($archiveDescriptionHtml) ?>"
                                            data-sermon-stream="<?= h($sermon['stream_url']) ?>"
                                            data-sermon-image="<?= h($archiveImageUrl) ?>"
                                            data-sermon-title-target="<?= h($featuredTitleId) ?>"
                                            data-sermon-meta-target="<?= h($featuredMetaId) ?>"
                                            data-sermon-description-target="<?= h($featuredDescriptionId) ?>"
                                            data-sermon-image-target="<?= h($featuredImageId) ?>"
                                            data-sermon-eyebrow-target="<?= h($featuredEyebrowId) ?>"
                                            data-sermon-id="<?= h($sermonId) ?>"
                                        >
                                            <?= h($sermon['title']) ?>
                                        </a>
                                    </h4>
                                    <?php if ($archiveMeta !== '') { ?>
                                        <p class="sermons-block__archive-meta" data-sermon-item-meta><?= h($archiveMeta) ?></p>
                                    <?php } else { ?>
                                        <p class="sermons-block__archive-meta" data-sermon-item-meta></p>
                                    <?php } ?>
                                </div>
                                <div class="sermons-block__archive-actions">
                                    <a
                                        class="sermons-block__listen-link sermons-block__archive-action-link"
                                        href="<?= h($sermon['stream_url']) ?>"
                                        data-sermon-load="<?= h($playerId) ?>"
                                        data-sermon-title="<?= h($sermon['title']) ?>"
                                        data-sermon-meta="<?= h($archiveMeta) ?>"
                                        data-sermon-description="<?= h($archiveDescriptionText) ?>"
                                        data-sermon-description-html="<?= h($archiveDescriptionHtml) ?>"
                                        data-sermon-stream="<?= h($sermon['stream_url']) ?>"
                                        data-sermon-image="<?= h($archiveImageUrl) ?>"
                                        data-sermon-title-target="<?= h($featuredTitleId) ?>"
                                        data-sermon-meta-target="<?= h($featuredMetaId) ?>"
                                        data-sermon-description-target="<?= h($featuredDescriptionId) ?>"
                                        data-sermon-image-target="<?= h($featuredImageId) ?>"
                                        data-sermon-eyebrow-target="<?= h($featuredEyebrowId) ?>"
                                        data-sermon-id="<?= h($sermonId) ?>"
                                    >
                                        <?= t('Listen') ?>
                                    </a>
                                </div>
                            </article>
                        <?php } ?>
                    </div>

                    <?php if (
                        $sourceType === 'spotify'
                        && (
                            ($showArchiveButton && $archiveButtonUrl !== '')
                            || ($showApplePodcastButton && $applePodcastButtonUrl !== '')
                        )
                    ) { ?>
                        <div class="sermons-block__archive-note">
                            <div class="sermons-block__archive-note-copy">
                                <p class="sermons-block__archive-note-title"><?= t('Want to keep listening?') ?></p>
                                <p class="sermons-block__archive-note-text"><?= t('Find more sermons in your podcast app.') ?></p>
                            </div>
                            <div class="sermons-block__archive-note-actions">
                                <?php if ($showApplePodcastButton && $applePodcastButtonUrl !== '') { ?>
                                    <a class="sermons-block__archive-note-link sermons-block__archive-note-link--apple" href="<?= h($applePodcastButtonUrl) ?>" target="_blank" rel="noopener noreferrer">
                                        <span class="sermons-block__apple-icon" aria-hidden="true"></span>
                                        <span><?= t('Apple Podcasts') ?></span>
                                    </a>
                                <?php } ?>
                                <?php if ($showArchiveButton && $archiveButtonUrl !== '') { ?>
                                    <a class="sermons-block__archive-note-link sermons-block__archive-note-link--spotify" href="<?= h($archiveButtonUrl) ?>" target="_blank" rel="noopener noreferrer">
                                        <span class="sermons-block__spotify-icon" aria-hidden="true"></span>
                                        <span><?= t('Spotify') ?></span>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="sermons-block__empty">
                <p><?= h($emptyMessage) ?></p>
            </div>
        <?php } ?>
    </div>
</section>
