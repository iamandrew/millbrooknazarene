<?php
defined('C5_EXECUTE') or die('Access denied.');

$form = app('helper/form');
$config = app('config');
?>

<div class="alert alert-info">
    Create a Turnstile widget for millbrooknazarene.church, then enter its keys here. The secret key is stored in Concrete's configuration and is not committed to the site repository.
</div>

<div class="form-group">
    <?= $form->label('site_key', t('Site Key')) ?>
    <?= $form->text('site_key', $config->get('captcha.turnstile.site_key')) ?>
</div>

<div class="form-group">
    <?= $form->label('secret_key', t('Secret Key')) ?>
    <?= $form->password('secret_key', '', ['autocomplete' => 'off', 'placeholder' => $config->get('captcha.turnstile.secret_key') ? t('Saved - enter a new value to replace it') : '']) ?>
</div>
