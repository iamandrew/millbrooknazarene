<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$page = Page::getByPath('/community/youth', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $page = Page::getByPath('/community/cheesy-nachos', 'ACTIVE');
}

if (!$page instanceof Page || $page->isError()) {
    $parent = Page::getByPath('/community', 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$parent instanceof Page || $parent->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve /community, page type, or full template.</error>');
        return 1;
    }

    $page = $parent->add(
        $pageType,
        [
            'cName' => 'Youth',
            'cHandle' => 'youth',
            'cDescription' => '',
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /community/youth.</info>');
}

$youthContent = require __DIR__ . '/content/youth.php';

$page->update([
    'cName' => $youthContent['name'],
    'cHandle' => 'youth',
    'cDescription' => $youthContent['description'],
]);
$page->rescanCollectionPath();
$page = Page::getByID($page->getCollectionID(), 'ACTIVE');

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $youthContent['content']]);

$heroImagePath = DIR_BASE . '/application/themes/millbrook/images/content/youth/youth-hero.webp';
$heroImage = find_or_import_youth_hero_image($heroImagePath);

if ($heroImage) {
    $heroImageKey = CollectionKey::getByHandle('hero_image');
    $disableHeroImageKey = CollectionKey::getByHandle('disable_hero_image');

    if ($heroImageKey) {
        $page->setAttribute($heroImageKey, $heroImage);
        $output->writeln('<info>Set Youth page hero image.</info>');
    } else {
        $output->writeln('<comment>Hero image attribute does not exist yet; run the hero attributes seed first.</comment>');
    }

    if ($disableHeroImageKey) {
        $page->setAttribute($disableHeroImageKey, false);
    }
} else {
    $output->writeln('<comment>Youth hero image not found at ' . $heroImagePath . '.</comment>');
}

$output->writeln('<info>Updated Youth page content from the leadership questionnaire.</info>');

return 0;

function find_or_import_youth_hero_image(string $path)
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

            $version->updateTitle('Youth hero');
            $version->updateDescription('Young people gathered at Millbrook Youth.');

            return $file;
        }
    }

    $version = app(FileImporter::class)->importLocalFile($path, $filename);
    $version->updateTitle('Youth hero');
    $version->updateDescription('Young people gathered at Millbrook Youth.');

    return $version->getFile();
}
