<?php

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Page\Page;

$homeDescription = 'A warm, family-friendly, Christ-centred church in Larne, living out faith in practical care for others.';
$socialImagePath = DIR_BASE . '/application/themes/millbrook/images/hero-1800.jpg';
$thumbnailKey = CollectionKey::getByHandle('thumbnail');

$site = \Core::make('site')->getSite();
$home = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');

if (!$home instanceof Page || $home->isError()) {
    $output->writeln('<error>Unable to find the site home page.</error>');
    return 1;
}

if ($home->getCollectionDescription() !== $homeDescription) {
    $home->update(['cDescription' => $homeDescription]);
    $output->writeln('<info>Updated home page meta description.</info>');
}

$socialImage = find_or_import_launch_social_image($socialImagePath);
if (!$socialImage) {
    $output->writeln('<comment>Social sharing image not found at ' . $socialImagePath . '.</comment>');
    return 0;
}

if (!$thumbnailKey) {
    $output->writeln('<comment>The thumbnail page attribute is not available; theme og:image fallback will still be used.</comment>');
    return 0;
}

$paths = [
    '/',
    '/visit-us',
    '/community',
    '/community/whats-on',
    '/community/homegroups',
    '/community/children',
    '/community/youth',
    '/community/mens-ministry',
    '/community/womens-ministry',
    '/community/creche',
    '/about',
    '/about/who-we-are',
    '/about/what-we-believe',
    '/resources',
    '/resources/sermons',
    '/resources/policies',
    '/giving',
    '/contact',
    '/kids-club-2026',
];

foreach ($paths as $path) {
    $page = $path === '/'
        ? $home
        : Page::getByPath($path, 'ACTIVE');

    if (!$page instanceof Page || $page->isError()) {
        $output->writeln(sprintf('<comment>Skipped missing page thumbnail: %s</comment>', $path));
        continue;
    }

    if ($page->getCollectionAttributeValue('thumbnail')) {
        continue;
    }

    $page->setAttribute($thumbnailKey, $socialImage);
    $output->writeln(sprintf('<info>Set social sharing image: %s</info>', $path));
}

$output->writeln('<info>Launch SEO metadata updated.</info>');

return 0;

function find_or_import_launch_social_image(string $path)
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

            $version->updateTitle('Millbrook Church social sharing image');
            $version->updateDescription('A Sunday gathering at Millbrook Church, used for social sharing previews.');

            return $file;
        }
    }

    $version = app(FileImporter::class)->importLocalFile($path, $filename);
    $version->updateTitle('Millbrook Church social sharing image');
    $version->updateDescription('A Sunday gathering at Millbrook Church, used for social sharing previews.');

    return $version->getFile();
}
