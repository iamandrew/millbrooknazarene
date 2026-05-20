<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
?>

<div class="form-group">
    <?php echo $form->label('title', t('Section Title')); ?>
    <?php echo $form->text('title', $title, ['maxlength' => 255]); ?>
</div>

<div class="form-group">
    <?php echo $form->label('intro', t('Intro Text')); ?>
    <?php echo $form->textarea('intro', $intro, ['rows' => 4]); ?>
</div>

<div class="form-group">
    <?php echo $form->label('folderUrl', t('Google Drive Folder URL or Folder ID')); ?>
    <?php echo $form->text('folderUrl', $folderUrl, ['placeholder' => 'https://drive.google.com/drive/folders/...']); ?>
    <p class="help-block"><?php echo t('Use a public Google Drive folder link. You can also paste the folder ID on its own.'); ?></p>
</div>

<div class="form-group">
    <?php echo $form->label('viewMode', t('Library Layout')); ?>
    <?php echo $form->select('viewMode', [
        'list' => t('List'),
        'grid' => t('Grid'),
    ], $viewMode); ?>
    <p class="help-block"><?php echo t('This block now renders a custom document library from the public Google Drive folder data.'); ?></p>
</div>

<div class="form-group">
    <div class="checkbox">
        <label>
            <?php echo $form->checkbox('showButton', 1, $showButton); ?>
            <?php echo t('Show button linking to the full folder'); ?>
        </label>
    </div>
</div>

<div class="form-group">
    <?php echo $form->label('buttonLabel', t('Button Label')); ?>
    <?php echo $form->text('buttonLabel', $buttonLabel, ['maxlength' => 255]); ?>
</div>
