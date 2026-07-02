<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$page = Page::getByPath('/community/womens-ministry', 'ACTIVE');
if (!$page instanceof Page || $page->isError()) {
    $page = Page::getByPath('/community/women', 'ACTIVE');
}

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /community/womens-ministry.</error>');
    return 1;
}

$womensMinistryContent = require __DIR__ . '/content/womens_ministry.php';

$page->update([
    'cName' => $womensMinistryContent['name'],
    'cHandle' => 'womens-ministry',
    'cDescription' => $womensMinistryContent['description'],
]);
$page->rescanCollectionPath();
$page = Page::getByID($page->getCollectionID(), 'ACTIVE');

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $womensMinistryContent['content']]);

$heroImagePath = DIR_BASE . '/application/themes/millbrook/images/content/womens/women-hero.webp';
$heroImage = find_or_import_women_hero_image($heroImagePath);

if ($heroImage) {
    $heroImageKey = CollectionKey::getByHandle('hero_image');
    $disableHeroImageKey = CollectionKey::getByHandle('disable_hero_image');

    if ($heroImageKey) {
        $page->setAttribute($heroImageKey, $heroImage);
        $output->writeln('<info>Set Women page hero image.</info>');
    } else {
        $output->writeln('<comment>Hero image attribute does not exist yet; run the hero attributes seed first.</comment>');
    }

    if ($disableHeroImageKey) {
        $page->setAttribute($disableHeroImageKey, false);
    }
} else {
    $output->writeln('<comment>Women hero image not found at ' . $heroImagePath . '.</comment>');
}

$output->writeln('<info>Updated Women page content from the Abide questionnaire.</info>');

return 0;

function find_or_import_women_hero_image(string $path)
{
    if (!is_file($path)) {
        return null;
    }

    $filename = basename($path);
    $list = new FileList();
    $list->ignorePermissions();
    $list->filterByKeywords($filename);

    foreach ($list->getResults() as $file) {
        $version = $file->getApprovedVersion();
        if ($version && $version->getFileName() === $filename) {
            $sourceHash = sha1_file($path);
            $existingContents = $version->getFileContents();
            $existingHash = $existingContents !== null ? sha1($existingContents) : '';

            if ($sourceHash !== $existingHash) {
                $version = $file->createNewVersion(true);
                $version->updateContents(file_get_contents($path));
            }

            $version->updateTitle('Abide Women hero');
            $version->updateDescription('Women from Abide gathered together.');

            return $file;
        }
    }

    $version = app(FileImporter::class)->importLocalFile($path, $filename);
    $version->updateTitle('Abide Women hero');
    $version->updateDescription('Women from Abide gathered together.');

    return $version->getFile();
}
