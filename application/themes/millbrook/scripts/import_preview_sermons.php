<?php

use Concrete\Core\File\FileList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Set\Set as FileSet;

$previewDirectory = DIR_BASE . '/tmp/sermons-preview';
$fileSet = FileSet::getByName('Sermons');

if (!$fileSet) {
    $output->writeln('<error>Could not find the Sermons file set.</error>');
    return 1;
}

if (!is_dir($previewDirectory)) {
    $output->writeln('<error>Could not find the preview sermons directory at ' . $previewDirectory . '.</error>');
    return 1;
}

$paths = glob($previewDirectory . '/*.{mp3,m4a,wav,ogg}', GLOB_BRACE) ?: [];
sort($paths);

if (!$paths) {
    $output->writeln('<error>No preview sermon files were found to import.</error>');
    return 1;
}

$existingByFilename = [];
$list = new FileList();
$list->ignorePermissions();
$list->filterBySet($fileSet);

foreach ($list->getResults() as $file) {
    $version = $file->getApprovedVersion();
    if ($version) {
        $existingByFilename[$version->getFileName()] = $file;
    }
}

$importer = app(FileImporter::class);
$imported = 0;
$updated = 0;

foreach ($paths as $path) {
    $filename = basename($path);
    $title = build_preview_sermon_title($filename);
    $description = '';

    if (isset($existingByFilename[$filename])) {
        $file = $existingByFilename[$filename];
        $version = $file->getApprovedVersion();
        if ($version) {
            $version->updateTitle($title);
            $version->updateDescription($description);
            $updated++;
            $output->writeln('<info>Updated existing preview sermon: ' . $title . '</info>');
        }
        continue;
    }

    $version = $importer->importLocalFile($path, $filename);
    $version->updateTitle($title);
    $version->updateDescription($description);
    $fileSet->addFileToSet($version->getFile());
    $imported++;
    $output->writeln('<info>Imported preview sermon: ' . $title . '</info>');
}

$output->writeln(sprintf('<info>Preview sermon import complete. Imported: %d, updated: %d.</info>', $imported, $updated));

return 0;

function build_preview_sermon_title(string $filename): string
{
    if (preg_match('/(\d{4})(\d{2})(\d{2})/', $filename, $matches)) {
        $date = DateTimeImmutable::createFromFormat('Ymd', $matches[1] . $matches[2] . $matches[3]);
        if ($date instanceof DateTimeImmutable) {
            return 'Sunday ' . $date->format('j M Y');
        }
    }

    $title = preg_replace('/\.[^.]+$/', '', $filename) ?: $filename;
    $title = str_replace(['_', '-'], ' ', $title);
    $title = preg_replace('/\s+/', ' ', $title) ?: $title;

    return trim($title) !== '' ? trim($title) : 'Preview sermon';
}
