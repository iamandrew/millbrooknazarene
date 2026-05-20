<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $title
 * @var string $intro
 * @var array<int,array<string,mixed>> $sermons
 * @var string $emptyMessage
 * @var bool $showDescriptions
 * @var bool $showPlayer
 * @var bool $showArchiveButton
 * @var string $archiveButtonLabel
 * @var string $archiveButtonUrl
 */
$featuredSermon = $sermons[0] ?? null;
$archiveSermons = $sermons;
$playerId = $featuredSermon ? 'sermons-player-' . (int) ($featuredSermon['id'] ?? 0) : '';
$featuredTitleId = $featuredSermon ? 'sermons-feature-title-' . (int) ($featuredSermon['id'] ?? 0) : '';
$featuredMetaId = $featuredSermon ? 'sermons-feature-meta-' . (int) ($featuredSermon['id'] ?? 0) : '';
$featuredDownloadId = $featuredSermon ? 'sermons-feature-download-' . (int) ($featuredSermon['id'] ?? 0) : '';
$featuredEyebrowId = $featuredSermon ? 'sermons-feature-eyebrow-' . (int) ($featuredSermon['id'] ?? 0) : '';

$formatSermonMeta = static function (array $sermon): string {
    $parts = [];

    if (!empty($sermon['date_label'])) {
        $parts[] = $sermon['date_label'];
    }

    if (!empty($sermon['speaker'])) {
        $parts[] = $sermon['speaker'];
    }

    return implode(' · ', $parts);
};
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
                <p class="sermons-block__eyebrow"><?= t('Recent teaching') ?></p>
                <p class="sermons-block__count">
                    <?= count($sermons) === 1 ? t('1 sermon') : t('%s sermons', count($sermons)) ?>
                </p>
            </div>

            <?php if ($showArchiveButton && $archiveButtonUrl !== '') { ?>
                <a class="button button--secondary sermons-block__archive-link" href="<?= h($archiveButtonUrl) ?>">
                    <?= h($archiveButtonLabel) ?>
                </a>
            <?php } ?>
        </div>

        <?php if ($sermons && $featuredSermon) { ?>
            <?php $featuredMeta = $formatSermonMeta($featuredSermon); ?>
            <article
                class="sermons-block__feature"
                data-featured-sermon
                data-sermon-id="<?= (int) $featuredSermon['id'] ?>"
                data-sermon-title="<?= h($featuredSermon['title']) ?>"
                data-sermon-meta="<?= h($featuredMeta) ?>"
                data-sermon-stream="<?= h($featuredSermon['stream_url']) ?>"
                data-sermon-download="<?= h($featuredSermon['download_url']) ?>"
            >
                <div class="sermons-block__feature-copy">
                    <p id="<?= h($featuredEyebrowId) ?>" class="sermons-block__feature-eyebrow"><?= t('Featured sermon') ?></p>
                    <h3 id="<?= h($featuredTitleId) ?>" class="sermons-block__feature-title"><?= h($featuredSermon['title']) ?></h3>
                    <?php if ($featuredMeta !== '') { ?>
                        <p id="<?= h($featuredMetaId) ?>" class="sermons-block__feature-meta"><?= h($featuredMeta) ?></p>
                    <?php } else { ?>
                        <p id="<?= h($featuredMetaId) ?>" class="sermons-block__feature-meta"></p>
                    <?php } ?>
                    <?php if ($showDescriptions && $featuredSermon['description'] !== '') { ?>
                        <p class="sermons-block__description"><?= h($featuredSermon['description']) ?></p>
                    <?php } ?>
                </div>

                <?php if ($showPlayer) { ?>
                    <div class="sermons-block__player-wrap sermons-block__player-wrap--feature">
                        <audio
                            id="<?= h($playerId) ?>"
                            class="sermons-block__player js-sermon-player"
                            controls
                            preload="none"
                            src="<?= h($featuredSermon['stream_url']) ?>"
                        >
                            <?= t('Your browser does not support audio playback.') ?>
                        </audio>
                    </div>
                <?php } ?>

                <div class="sermons-block__actions">
                    <?php if ($showPlayer && $playerId !== '') { ?>
                        <button class="button button--primary sermons-block__play-button" type="button" data-sermon-play="<?= h($playerId) ?>">
                            <?= t('Listen now') ?>
                        </button>
                    <?php } else { ?>
                        <a class="button button--primary" href="<?= h($featuredSermon['stream_url']) ?>">
                            <?= t('Listen now') ?>
                        </a>
                    <?php } ?>
                    <a id="<?= h($featuredDownloadId) ?>" class="text-link" href="<?= h($featuredSermon['download_url']) ?>">
                        <?= t('Download audio') ?>
                    </a>
                </div>
            </article>

            <?php if ($archiveSermons) { ?>
                <div class="sermons-block__archive">
                    <div class="sermons-block__archive-heading">
                        <p class="sermons-block__archive-eyebrow"><?= t('More recent teaching') ?></p>
                    </div>

                    <div class="sermons-block__archive-list">
                        <?php foreach ($archiveSermons as $sermon) { ?>
                            <?php $archiveMeta = $formatSermonMeta($sermon); ?>
                            <article
                                class="sermons-block__archive-item<?= $featuredSermon && (int) $featuredSermon['id'] === (int) $sermon['id'] ? ' is-active' : '' ?>"
                                data-sermon-item
                                data-sermon-id="<?= (int) $sermon['id'] ?>"
                                data-sermon-title="<?= h($sermon['title']) ?>"
                                data-sermon-meta="<?= h($archiveMeta) ?>"
                                data-sermon-stream="<?= h($sermon['stream_url']) ?>"
                                data-sermon-download="<?= h($sermon['download_url']) ?>"
                            >
                                <div class="sermons-block__archive-copy">
                                    <h4 class="sermons-block__archive-title" data-sermon-item-title><?= h($sermon['title']) ?></h4>
                                    <?php if ($archiveMeta !== '') { ?>
                                        <p class="sermons-block__archive-meta" data-sermon-item-meta><?= h($archiveMeta) ?></p>
                                    <?php } else { ?>
                                        <p class="sermons-block__archive-meta" data-sermon-item-meta></p>
                                    <?php } ?>
                                </div>
                                <div class="sermons-block__archive-actions">
                                    <a
                                        class="text-link"
                                        href="<?= h($sermon['stream_url']) ?>"
                                        data-sermon-load="<?= h($playerId) ?>"
                                        data-sermon-title="<?= h($sermon['title']) ?>"
                                        data-sermon-meta="<?= h($archiveMeta) ?>"
                                        data-sermon-stream="<?= h($sermon['stream_url']) ?>"
                                        data-sermon-download="<?= h($sermon['download_url']) ?>"
                                        data-sermon-title-target="<?= h($featuredTitleId) ?>"
                                        data-sermon-meta-target="<?= h($featuredMetaId) ?>"
                                        data-sermon-download-target="<?= h($featuredDownloadId) ?>"
                                        data-sermon-eyebrow-target="<?= h($featuredEyebrowId) ?>"
                                        data-sermon-id="<?= (int) $sermon['id'] ?>"
                                    >
                                        <?= t('Listen') ?>
                                    </a>
                                    <a class="text-link" href="<?= h($sermon['download_url']) ?>" data-sermon-item-download>
                                        <?= t('Download') ?>
                                    </a>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="sermons-block__empty">
                <p><?= h($emptyMessage) ?></p>
            </div>
        <?php } ?>
    </div>
</section>
