<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $sourceType
 * @var string $spotifyFeedUrl
 * @var string $defaultSpotifyShowUrl
 * @var string $defaultSpotifyFeedUrl
 * @var string $defaultApplePodcastUrl
 * @var int $displayLimit
 * @var bool $showDescriptions
 * @var bool $showPlayer
 * @var bool $showArchiveButton
 */
?>

<div class="form-group">
    <?= $form->label('title', t('Section title')) ?>
    <?= $form->text('title', $title) ?>
</div>

<div class="form-group">
    <?= $form->label('intro', t('Intro')) ?>
    <?= $form->textarea('intro', $intro, ['rows' => 3]) ?>
</div>

<div class="form-group">
    <?= $form->label('sourceType', t('Content source')) ?>
    <?= $form->select('sourceType', [
        'concrete_uploads' => t('Sermon Express entries'),
        'spotify' => t('Spotify podcast RSS feed'),
    ], $sourceType ?? 'concrete_uploads') ?>
    <small class="form-text text-muted">
        <?= t('Use the RSS feed from Spotify for Creators to show full episodes with this site styling.') ?>
    </small>
</div>

<div class="form-group" data-spotify-feed-field>
    <?= $form->label('spotifyFeedUrl', t('Podcast RSS feed URL')) ?>
    <?= $form->text('spotifyFeedUrl', $spotifyFeedUrl ?? '', ['placeholder' => $defaultSpotifyFeedUrl ?? 'https://.../podcast/rss']) ?>
    <small class="form-text text-muted">
        <?= t('A public Spotify show link is useful for the Spotify button, but the RSS feed is needed for audio playback.') ?>
    </small>
</div>

<div class="form-group">
    <?= $form->label('displayLimit', t('Number of sermons to show')) ?>
    <?= $form->number('displayLimit', $displayLimit, ['min' => 1, 'max' => 8]) ?>
    <small class="form-text text-muted">
        <?= t('The page shows up to 8 sermons; use the Spotify button for more.') ?>
    </small>
</div>

<div class="form-group">
    <div class="form-check">
        <?= $form->checkbox('showDescriptions', 1, $showDescriptions) ?>
        <?= $form->label('showDescriptions', t('Show episode descriptions'), ['class' => 'form-check-label']) ?>
    </div>
</div>

<div class="form-group">
    <div class="form-check">
        <?= $form->checkbox('showPlayer', 1, $showPlayer) ?>
        <?= $form->label('showPlayer', t('Show audio player'), ['class' => 'form-check-label']) ?>
    </div>
</div>

<div class="form-group">
    <div class="form-check">
        <?= $form->checkbox('showArchiveButton', 1, $showArchiveButton) ?>
        <?= $form->label('showArchiveButton', t('Show Spotify button'), ['class' => 'form-check-label']) ?>
    </div>
</div>

<div class="form-group">
    <?= $form->label('archiveButtonLabel', t('Button label')) ?>
    <?= $form->text('archiveButtonLabel', $archiveButtonLabel) ?>
</div>

<div class="form-group">
    <?= $form->label('archiveButtonUrl', t('Button URL')) ?>
    <?= $form->text('archiveButtonUrl', $archiveButtonUrl, ['placeholder' => $defaultSpotifyShowUrl ?? 'https://open.spotify.com/show/...']) ?>
    <small class="form-text text-muted">
        <?= t('For Spotify podcasts, use the public Spotify show link here.') ?>
    </small>
</div>

<hr>

<div class="form-group">
    <?= $form->label('applePodcastButtonLabel', t('Apple Podcasts button label')) ?>
    <?= $form->text('applePodcastButtonLabel', $applePodcastButtonLabel) ?>
</div>

<div class="form-group">
    <?= $form->label('applePodcastButtonUrl', t('Apple Podcasts URL')) ?>
    <?= $form->text('applePodcastButtonUrl', $applePodcastButtonUrl, ['placeholder' => $defaultApplePodcastUrl ?? 'https://podcasts.apple.com/...']) ?>
    <small class="form-text text-muted">
        <?= t('Leave this blank to hide the Apple Podcasts button.') ?>
    </small>
</div>

<script>
(function () {
    var source = document.getElementById('sourceType');
    var feedField = document.querySelector('[data-spotify-feed-field]');

    if (!source || !feedField) {
        return;
    }

    var syncFeedField = function () {
        feedField.style.display = source.value === 'spotify' ? '' : 'none';
    };

    source.addEventListener('change', syncFeedField);
    syncFeedField();
}());
</script>
