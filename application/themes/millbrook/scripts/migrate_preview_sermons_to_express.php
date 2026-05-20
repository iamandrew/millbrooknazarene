<?php

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Set\Set as FileSet;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = app(EntityManagerInterface::class);
$objectManager = app(ObjectManager::class);

$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'sermon']);
if (!$entity) {
    $output->writeln('<error>Could not find the Sermon Express entity.</error>');
    return 1;
}

$fileSet = FileSet::getByName('Sermons');
if (!$fileSet) {
    $output->writeln('<error>Could not find the Sermons file set.</error>');
    return 1;
}

$list = new FileList();
$list->ignorePermissions();
$list->filterBySet($fileSet);
$list->sortByFilenameAscending();

$files = $list->getResults();
if (!$files) {
    $output->writeln('<comment>No sermon files were found in the Sermons file set.</comment>');
    return 0;
}

$existingByFileId = [];
$existingByTitle = [];

$entryList = new EntryList($entity);
$entryList->ignorePermissions();

foreach ($entryList->getResults() as $entry) {
    if (!$entry instanceof Entry) {
        continue;
    }

    $title = trim((string) $entry->getAttribute('sermon_title'));
    if ($title !== '') {
        $existingByTitle[mb_strtolower($title)] = $entry;
    }

    $audio = $entry->getAttribute('audio_file');
    $entryFile = null;

    if ($audio instanceof FileEntity) {
        $entryFile = $audio;
    } elseif ($audio instanceof Version) {
        $entryFile = $audio->getFile();
    }

    if ($entryFile instanceof FileEntity) {
        $existingByFileId[(int) $entryFile->getFileID()] = $entry;
    }
}

$created = 0;
$skipped = 0;

foreach ($files as $file) {
    if (!$file instanceof FileEntity) {
        continue;
    }

    $version = $file->getApprovedVersion();
    if (!$version instanceof Version) {
        continue;
    }

    $title = build_preview_sermon_title($version->getFileName(), (string) $version->getTitle());
    $normalizedTitle = mb_strtolower($title);
    $fileId = (int) $file->getFileID();

    $date = build_preview_sermon_date($version->getFileName(), $version);
    $existingEntry = $existingByFileId[$fileId] ?? $existingByTitle[$normalizedTitle] ?? null;
    if ($existingEntry instanceof Entry) {
        if (sermon_entry_needs_rebuild($existingEntry, $date)) {
            $objectManager->deleteEntry($existingEntry);
            $output->writeln(sprintf('<comment>Rebuilding incomplete sermon entry: %s</comment>', $title));
        } else {
            sync_existing_sermon_entry($existingEntry, $title, $file, $date);
            $skipped++;
            $output->writeln(sprintf('<comment>Updated existing sermon entry: %s</comment>', $title));
            continue;
        }
    }

    $builder = $objectManager->buildEntry($entity);
    $builder->setAttribute('sermon_title', $title);
    $builder->setAttribute('speaker', '');
    $builder->setAttribute('audio_file', $file);

    if ($date instanceof DateTimeInterface) {
        $builder->setAttribute('date', $date);
    }

    $builder->save();
    $created++;
    $output->writeln(sprintf('<info>Created sermon entry: %s</info>', $title));
}

$output->writeln(sprintf(
    '<info>Preview sermon migration complete. Created: %d, skipped: %d.</info>',
    $created,
    $skipped
));

return 0;

function sermon_entry_needs_rebuild(Entry $entry, ?DateTimeInterface $date = null): bool
{
    if (!$date instanceof DateTimeInterface) {
        return false;
    }

    $existingDate = $entry->getAttribute('date');

    return !$existingDate instanceof DateTimeInterface && (!is_string($existingDate) || trim($existingDate) === '');
}

function sync_existing_sermon_entry(Entry $entry, string $title, FileEntity $file, ?DateTimeInterface $date = null): void
{
    $entry->setAttribute('sermon_title', $title);
    $entry->setAttribute('audio_file', $file);

    if ($date instanceof DateTimeInterface) {
        $entry->setAttribute('date', $date);
    }
}

function build_preview_sermon_title(string $filename, string $existingTitle = ''): string
{
    $existingTitle = trim($existingTitle);
    if ($existingTitle !== '') {
        return $existingTitle;
    }

    if (preg_match('/(\d{4})(\d{2})(\d{2})/', $filename, $matches)) {
        $date = DateTimeImmutable::createFromFormat('Ymd', $matches[1] . $matches[2] . $matches[3]);
        if ($date instanceof DateTimeImmutable) {
            return 'Sunday ' . $date->format('j M Y');
        }
    }

    $title = preg_replace('/\.[^.]+$/', '', $filename) ?: $filename;
    $title = str_replace(['_', '-'], ' ', $title);
    $title = preg_replace('/\s+/', ' ', $title) ?: $title;
    $title = trim($title);

    return $title !== '' ? $title : 'Preview sermon';
}

function build_preview_sermon_date(string $filename, Version $version): ?DateTimeInterface
{
    if (preg_match('/(\d{4})(\d{2})(\d{2})/', $filename, $matches)) {
        $date = DateTimeImmutable::createFromFormat('Ymd', $matches[1] . $matches[2] . $matches[3]);
        if ($date instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($date);
        }
    }

    $dateAdded = $version->getDateAdded();
    if ($dateAdded instanceof DateTimeInterface) {
        return $dateAdded instanceof DateTime ? clone $dateAdded : DateTime::createFromImmutable(DateTimeImmutable::createFromInterface($dateAdded));
    }

    return null;
}
