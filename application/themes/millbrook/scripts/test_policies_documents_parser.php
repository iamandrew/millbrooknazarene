<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/resources/policies', 'ACTIVE');

if (!$page || $page->isError()) {
    $output->writeln('<error>Policies page not found.</error>');
    return 1;
}

$area = Area::getOrCreate($page, 'Main');

foreach ($area->getAreaBlocksArray($page) as $block) {
    if ($block->getBlockTypeHandle() !== 'google_drive_documents') {
        continue;
    }

    $controller = $block->getController();
    $reflection = new ReflectionClass($controller);
    $extractFolderId = $reflection->getMethod('extractFolderId');
    $extractFolderId->setAccessible(true);
    $fetchFolderDocuments = $reflection->getMethod('fetchFolderDocuments');
    $fetchFolderDocuments->setAccessible(true);

    $folderId = $extractFolderId->invoke($controller, (string) $controller->folderUrl);
    $documents = $fetchFolderDocuments->invoke($controller, $folderId);

    $output->writeln(sprintf('Found %d documents.', count($documents)));
    foreach (array_slice($documents, 0, 5) as $document) {
        $output->writeln(sprintf('- %s (%s)', $document['name'], $document['modified_label']));
    }

    return 0;
}

$output->writeln('<error>No Google Drive Documents block found.</error>');

return 1;
