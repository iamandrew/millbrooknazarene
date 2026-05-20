<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var int $displayLimit
 * @var bool $showPlayer
 * @var bool $showArchiveButton
 */
?>

<?= $form->hidden('sourceType', $sourceType ?? 'concrete_uploads') ?>

<div class="form-group">
    <?= $form->label('title', t('Section title')) ?>
    <?= $form->text('title', $title) ?>
</div>

<div class="form-group">
    <?= $form->label('intro', t('Intro')) ?>
    <?= $form->textarea('intro', $intro, ['rows' => 3]) ?>
</div>

<div class="form-group">
    <?= $form->label('entityInfo', t('Content source')) ?>
    <div class="form-control-static">
        <?= t('This block now reads from the Sermon Express entity using the fields: Sermon Title, Speaker, Audio File, and Date.') ?>
    </div>
    <small class="form-text text-muted">
        <?= t('Manage sermons in Express and this page will update automatically from those entries.') ?>
    </small>
</div>

<div class="form-group">
    <?= $form->label('displayLimit', t('Number of sermons to show')) ?>
    <?= $form->number('displayLimit', $displayLimit, ['min' => 1, 'max' => 24]) ?>
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
        <?= $form->label('showArchiveButton', t('Show archive button'), ['class' => 'form-check-label']) ?>
    </div>
</div>

<div class="form-group">
    <?= $form->label('archiveButtonLabel', t('Archive button label')) ?>
    <?= $form->text('archiveButtonLabel', $archiveButtonLabel) ?>
</div>

<div class="form-group">
    <?= $form->label('archiveButtonUrl', t('Archive button URL')) ?>
    <?= $form->text('archiveButtonUrl', $archiveButtonUrl, ['placeholder' => '/resources/sermons']) ?>
    <small class="form-text text-muted">
        <?= t('Later on, this block can switch to another source such as Spotify without changing the page layout.') ?>
    </small>
</div>
