<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $layout
 * @var bool $hasSharedSource
 */
?>

<div class="alert alert-info">
    <?php if (!empty($hasSharedSource)) { ?>
        <?= t('What’s On items are now managed in the shared Express source, so changes there will appear anywhere this block is used.') ?>
    <?php } else { ?>
        <?= t('This block is ready for a shared Express source, but no What’s On Item entity was found yet. The legacy block data will be used until that source exists.') ?>
    <?php } ?>
</div>

<div class="form-group">
    <?= $form->label('title', t('Section title')) ?>
    <?= $form->text('title', $title) ?>
</div>

<div class="form-group">
    <?= $form->label('intro', t('Intro')) ?>
    <?= $form->textarea('intro', $intro, ['rows' => 3]) ?>
</div>

<div class="form-group">
    <?= $form->label('layout', t('Layout')) ?>
    <?= $form->select('layout', [
        'cards' => t('Full cards'),
        'compact' => t('Compact list'),
    ], $layout) ?>
    <p class="help-block"><?= t('Compact mode is intended for the homepage and shows the first few shared items. Full cards mode is intended for the main What’s On page.') ?></p>
</div>

<div class="form-group">
    <?= $form->label('primaryButtonLabel', t('Primary button label')) ?>
    <?= $form->text('primaryButtonLabel', $primaryButtonLabel, ['placeholder' => t('Visit Us?')]) ?>
</div>

<div class="form-group">
    <?= $form->label('primaryButtonUrl', t('Primary button URL')) ?>
    <?= $form->text('primaryButtonUrl', $primaryButtonUrl, ['placeholder' => '/visit-us']) ?>
</div>

<div class="form-group">
    <?= $form->label('secondaryButtonLabel', t('Secondary button label')) ?>
    <?= $form->text('secondaryButtonLabel', $secondaryButtonLabel, ['placeholder' => t('Latest Sermons')]) ?>
</div>

<div class="form-group">
    <?= $form->label('secondaryButtonUrl', t('Secondary button URL')) ?>
    <?= $form->text('secondaryButtonUrl', $secondaryButtonUrl, ['placeholder' => '/resources/sermons']) ?>
</div>
