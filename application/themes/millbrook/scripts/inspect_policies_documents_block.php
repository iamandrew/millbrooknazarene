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
    $output->writeln(sprintf(
        'Block #%d [%s]',
        $block->getBlockID(),
        $block->getBlockTypeHandle()
    ));

    if ($block->getBlockTypeHandle() === 'google_drive_documents') {
        $controller = $block->getController();
        $output->writeln(sprintf('  title: %s', (string) ($controller->title ?? '')));
        $output->writeln(sprintf('  intro: %s', (string) ($controller->intro ?? '')));
        $output->writeln(sprintf('  folderUrl: %s', (string) ($controller->folderUrl ?? '')));
        $output->writeln(sprintf('  viewMode: %s', (string) ($controller->viewMode ?? '')));
        $output->writeln(sprintf('  embedHeight: %s', (string) ($controller->embedHeight ?? '')));
    }
}

return 0;
