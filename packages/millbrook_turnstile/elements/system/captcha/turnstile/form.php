<?php
defined('C5_EXECUTE') or die('Access denied.');

$form = app('helper/form');
$config = app('config');
?>

<div class="alert alert-info">
    The Turnstile site key is stored here. Set the secret key as the TURNSTILE_SECRET environment variable on the web server; it is not stored in Concrete or the site repository.
</div>

<div class="form-group">
    <?= $form->label('site_key', t('Site Key')) ?>
    <?= $form->text('site_key', $config->get('captcha.turnstile.site_key')) ?>
</div>
