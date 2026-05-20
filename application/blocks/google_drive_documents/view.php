<?php
defined('C5_EXECUTE') or die('Access Denied.');

$hasFolder = $folderId !== '';
$folderHref = $folderUrl ?: ($hasFolder ? 'https://drive.google.com/drive/folders/' . rawurlencode($folderId) : '');
$isEditMode = isset($c) && method_exists($c, 'isEditMode') && $c->isEditMode();
$layoutClass = $viewMode === 'grid' ? 'drive-documents-block__documents--grid' : 'drive-documents-block__documents--list';
?>

<section class="drive-documents-block">
    <?php if ($title !== '' || $intro !== '') { ?>
        <div class="drive-documents-block__header">
            <?php if ($title !== '') { ?>
                <h2 class="drive-documents-block__title"><?php echo h($title); ?></h2>
            <?php } ?>

            <?php if ($intro !== '') { ?>
                <div class="drive-documents-block__intro">
                    <?php echo nl2br(h($intro)); ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if ($hasFolder && $documents !== []) { ?>
        <div class="drive-documents-block__toolbar">
            <div class="drive-documents-block__toolbar-copy">
                <p class="drive-documents-block__eyebrow"><?php echo t('Document library'); ?></p>
                <p class="drive-documents-block__count">
                    <?php echo t2('%d document', '%d documents', count($documents), count($documents)); ?>
                </p>
            </div>

            <?php if ($showButton && $folderHref !== '') { ?>
                <a class="button button--secondary drive-documents-block__toolbar-link" href="<?php echo h($folderHref); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo h($buttonLabel); ?>
                </a>
            <?php } ?>
        </div>

        <div class="drive-documents-block__documents <?php echo h($layoutClass); ?>">
            <?php foreach ($documents as $document) { ?>
                <article class="drive-documents-block__document">
                    <div class="drive-documents-block__document-main">
                        <span class="drive-documents-block__badge"><?php echo h($document['extension']); ?></span>

                        <div class="drive-documents-block__document-copy">
                            <h3 class="drive-documents-block__document-title">
                                <a href="<?php echo h($document['view_url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo h($document['display_name']); ?>
                                </a>
                            </h3>

                            <p class="drive-documents-block__document-meta">
                                <?php if ($document['modified_label'] !== '') { ?>
                                    <span><?php echo t('Updated %s', h($document['modified_label'])); ?></span>
                                <?php } ?>
                                <?php if ($document['modified_label'] !== '' && $document['size_label'] !== '') { ?>
                                    <span aria-hidden="true">·</span>
                                <?php } ?>
                                <?php if ($document['size_label'] !== '') { ?>
                                    <span><?php echo h($document['size_label']); ?></span>
                                <?php } ?>
                            </p>
                        </div>
                    </div>

                    <div class="drive-documents-block__document-actions">
                        <a class="text-link" href="<?php echo h($document['view_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo t('Open'); ?>
                        </a>
                        <a class="drive-documents-block__download" href="<?php echo h($document['download_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo t('Download'); ?>
                        </a>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } elseif ($hasFolder && $documentsError !== '') { ?>
        <div class="drive-documents-block__empty">
            <p><?php echo h($documentsError); ?></p>
            <?php if ($showButton && $folderHref !== '') { ?>
                <div class="drive-documents-block__actions">
                    <a class="button button--primary" href="<?php echo h($folderHref); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo h($buttonLabel); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="drive-documents-block__empty">
            <p><?php echo t('Key documents will appear here soon.'); ?></p>
            <?php if ($isEditMode) { ?>
                <p><?php echo t('Add a public Google Drive folder URL or folder ID in the block settings to display documents here.'); ?></p>
            <?php } ?>
        </div>
    <?php } ?>
</section>
