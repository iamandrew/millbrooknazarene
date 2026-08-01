<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;

/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Core\Express\Form\Renderer|null $renderer */
/** @var string|null $success */
/** @var string $bID */
/** @var \Concrete\Core\Error\ErrorList\ErrorList|null $error */
/** @var \Concrete\Core\Captcha\CaptchaInterface|null $captcha */
/** @var string $displayCaptcha */
/** @var string $submitLabel */

$requiredAttributeIds = [];
if (isset($renderer)) {
    foreach ($renderer->getContext()->getForm()->getFieldSets() as $fieldSet) {
        foreach ($fieldSet->getControls() as $control) {
            if ($control instanceof AttributeKeyControl && $control->isRequired()) {
                $requiredAttributeIds[] = $control->getAttributeKey()->getAttributeKeyID();
            }
        }
    }
}
?>
<div class="ccm-block-express-form">
    <?php if (isset($renderer)) { ?>
        <div class="ccm-form">
            <a name="form<?= $bID ?>"></a>

            <?php if (isset($success)) { ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php } ?>

            <?php if (isset($error) && is_object($error)) { ?>
                <div class="alert alert-danger"><?= $error->output() ?></div>
            <?php } ?>

            <form id="express-form-<?= $bID ?>" enctype="multipart/form-data" class="form-stacked" method="post"
                  action="<?= $view->action('submit') ?>#form<?= $bID ?>">
                <?php $renderer->render(); ?>

                <script>
                    (function (form) {
                        const requiredAttributeIds = <?= json_encode($requiredAttributeIds) ?>;
                        requiredAttributeIds.forEach(function (attributeId) {
                            form.querySelectorAll('[name^="akID[' + attributeId + ']"]').forEach(function (field) {
                                if (!field.disabled) {
                                    field.required = true;
                                    field.setAttribute('aria-required', 'true');
                                }
                            });
                        });
                    })(document.getElementById('express-form-<?= $bID ?>'));
                </script>

                <?php if ($displayCaptcha) { ?>
                    <div class="form-group captcha">
                        <?php $captchaLabel = $captcha->label(); ?>
                        <?php if (!empty($captchaLabel)) { ?>
                            <label class="control-label form-label"><?= $captchaLabel ?></label>
                        <?php } ?>
                        <div><?php $captcha->display(); ?></div>
                        <div><?php $captcha->showInput(); ?></div>
                    </div>
                <?php } ?>

                <div class="form-actions">
                    <button type="submit" name="Submit" class="btn btn-primary"><?= t($submitLabel) ?></button>
                </div>
            </form>
        </div>
    <?php } else { ?>
        <p><?= t('This form is unavailable.') ?></p>
    <?php } ?>
</div>
