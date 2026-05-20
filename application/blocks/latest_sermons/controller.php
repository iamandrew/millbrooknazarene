<?php

namespace Application\Block\LatestSermons;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected $btTable = 'btLatestSermons';
    protected $btInterfaceWidth = 720;
    protected $btInterfaceHeight = 540;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public function getBlockTypeName(): string
    {
        return t('Latest Sermons');
    }

    public function getBlockTypeDescription(): string
    {
        return t('Display recent sermon audio from the Sermon Express entity, ready to swap to another source later.');
    }

    public function add(): void
    {
        $this->setDefaults();
    }

    public function edit(): void
    {
        $this->setDefaults();
    }

    public function view(): void
    {
        $title = trim((string) $this->title);
        $intro = trim((string) $this->intro);
        $sourceType = $this->getValidSourceType((string) $this->sourceType);
        $displayLimit = $this->getValidDisplayLimit((int) $this->displayLimit);
        $showPlayer = !empty($this->showPlayer);
        $showArchiveButton = !empty($this->showArchiveButton);
        $archiveButtonLabel = trim((string) $this->archiveButtonLabel) ?: t('Latest Sermons');
        $archiveButtonUrl = trim((string) $this->archiveButtonUrl);
        $sermons = $this->getSermons($sourceType, $displayLimit);
        $entity = $this->getSermonEntity();

        $this->set('title', $title);
        $this->set('intro', $intro);
        $this->set('sourceType', $sourceType);
        $this->set('displayLimit', $displayLimit);
        $this->set('showPlayer', $showPlayer);
        $this->set('showArchiveButton', $showArchiveButton && $archiveButtonUrl !== '');
        $this->set('archiveButtonLabel', $archiveButtonLabel);
        $this->set('archiveButtonUrl', $archiveButtonUrl);
        $this->set('entity', $entity);
        $this->set('sermons', $sermons);
        $this->set('emptyMessage', $this->getEmptyMessage($entity));
    }

    public function save($args): void
    {
        $args['title'] = trim((string) ($args['title'] ?? ''));
        $args['intro'] = trim((string) ($args['intro'] ?? ''));
        $args['sourceType'] = $this->getValidSourceType((string) ($args['sourceType'] ?? 'concrete_uploads'));
        $args['displayLimit'] = $this->getValidDisplayLimit((int) ($args['displayLimit'] ?? 6));
        $args['showPlayer'] = !empty($args['showPlayer']) ? 1 : 0;
        $args['showArchiveButton'] = !empty($args['showArchiveButton']) ? 1 : 0;
        $args['archiveButtonLabel'] = trim((string) ($args['archiveButtonLabel'] ?? ''));
        $args['archiveButtonUrl'] = trim((string) ($args['archiveButtonUrl'] ?? ''));

        parent::save($args);
    }

    protected function setDefaults(): void
    {
        $this->set('title', $this->title ?: t('Latest Sermons'));
        $this->set('intro', $this->intro ?: '');
        $this->set('sourceType', $this->getValidSourceType((string) ($this->sourceType ?: 'concrete_uploads')));
        $this->set('displayLimit', $this->getValidDisplayLimit((int) ($this->displayLimit ?: 6)));
        $this->set('showPlayer', isset($this->showPlayer) ? (bool) $this->showPlayer : true);
        $this->set('showArchiveButton', isset($this->showArchiveButton) ? (bool) $this->showArchiveButton : true);
        $this->set('archiveButtonLabel', $this->archiveButtonLabel ?: t('Latest Sermons'));
        $this->set('archiveButtonUrl', $this->archiveButtonUrl ?: '/resources/sermons');
    }

    protected function getValidSourceType(string $sourceType): string
    {
        return in_array($sourceType, ['concrete_uploads', 'spotify'], true) ? $sourceType : 'concrete_uploads';
    }

    protected function getValidDisplayLimit(int $limit): int
    {
        return max(1, min($limit, 24));
    }

    protected function getSermonEntity(): ?Entity
    {
        $entityManager = app(EntityManagerInterface::class);

        return $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'sermon']);
    }

    protected function getSermons(string $sourceType, int $limit): array
    {
        if ($sourceType === 'spotify') {
            return [];
        }

        return $this->getSermonsFromConcreteUploads($limit);
    }

    protected function getSermonsFromConcreteUploads(int $limit): array
    {
        $entity = $this->getSermonEntity();
        if (!$entity) {
            return [];
        }

        $list = new EntryList($entity);
        $list->ignorePermissions();
        $list->sortByDateAddedDescending();

        $sermons = [];

        foreach ($list->getResults() as $entry) {
            if (!$entry instanceof Entry) {
                continue;
            }

            $sermon = $this->buildSermonItemFromEntry($entry);
            if ($sermon === null) {
                continue;
            }

            $sermons[] = $sermon;
        }

        usort(
            $sermons,
            static function (array $a, array $b): int {
                return ($b['preached_at_timestamp'] ?? 0) <=> ($a['preached_at_timestamp'] ?? 0);
            }
        );

        $sermons = array_slice($sermons, 0, $limit);

        return $sermons;
    }

    protected function isAudioVersion(Version $version): bool
    {
        $mimeType = (string) $version->getMimeType();
        if ($mimeType !== '' && strpos($mimeType, 'audio/') === 0) {
            return true;
        }

        return in_array(
            strtolower((string) $version->getExtension()),
            ['mp3', 'm4a', 'aac', 'wav', 'ogg', 'oga', 'flac'],
            true
        );
    }

    protected function buildSermonItemFromEntry(Entry $entry): ?array
    {
        $audio = $entry->getAttribute('audio_file');
        $file = $audio instanceof FileEntity ? $audio : null;
        if (!$file && $audio instanceof Version) {
            $file = $audio->getFile();
        }
        if (!$file instanceof FileEntity) {
            return null;
        }

        $version = $file->getApprovedVersion();
        if (!$version instanceof Version || !$this->isAudioVersion($version)) {
            return null;
        }

        $title = trim((string) $entry->getAttribute('sermon_title'));
        if ($title === '') {
            $title = trim((string) $version->getTitle());
        }
        if ($title === '') {
            $title = $this->formatSermonTitle((string) $version->getFileName());
        }

        $speaker = trim((string) $entry->getAttribute('speaker'));
        $dateValue = $entry->getAttribute('date');
        $preachedAtTimestamp = 0;
        $dateLabel = '';

        if ($dateValue instanceof \DateTimeInterface) {
            $preachedAtTimestamp = $dateValue->getTimestamp();
            $dateLabel = $dateValue->format('j M Y');
        } elseif (is_string($dateValue) && trim($dateValue) !== '') {
            try {
                $date = new \DateTimeImmutable($dateValue);
                $preachedAtTimestamp = $date->getTimestamp();
                $dateLabel = $date->format('j M Y');
            } catch (\Throwable $exception) {
                $dateLabel = trim($dateValue);
            }
        }

        if ($preachedAtTimestamp === 0) {
            $dateAdded = $version->getDateAdded();
            if ($dateAdded instanceof \DateTimeInterface) {
                $preachedAtTimestamp = $dateAdded->getTimestamp();
                if ($dateLabel === '') {
                    $dateLabel = $dateAdded->format('j M Y');
                }
            }
        }

        return [
            'id' => (int) $entry->getID(),
            'title' => $title,
            'speaker' => $speaker,
            'description' => '',
            'date_label' => $dateLabel,
            'preached_at_timestamp' => $preachedAtTimestamp,
            'stream_url' => (string) $version->getURL(),
            'download_url' => (string) $version->getDownloadURL(),
        ];
    }

    protected function formatSermonTitle(string $filename): string
    {
        $title = preg_replace('/\.[^.]+$/', '', $filename) ?: $filename;
        $title = str_replace(['_', '-'], ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title) ?: $title;
        $title = trim($title);

        return $title !== '' ? $title : t('Untitled sermon');
    }

    protected function getEmptyMessage(?Entity $entity): string
    {
        if ($entity) {
            return t(
                'Add sermon entries to the %s Express entity to populate this section.',
                $entity->getName()
            );
        }

        return t('Create the Sermon Express entity to begin showing sermons here.');
    }
}
