<?php

namespace Application\Block\LatestSermons;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Feed\FeedService;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected const DEFAULT_SPOTIFY_SHOW_URL = 'https://open.spotify.com/show/033njtKzXFC2vPB33mR1UV';
    protected const DEFAULT_SPOTIFY_FEED_URL = 'https://anchor.fm/s/113054664/podcast/rss';

    protected $btTable = 'btLatestSermons';
    protected $btInterfaceWidth = 720;
    protected $btInterfaceHeight = 540;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $rssFeedCacheLifetime = 1800;
    protected $feedErrorMessage = '';

    /**
     * @var array<string,array<int,array<string,mixed>>>
     */
    protected static $spotifySermonsCache = [];

    public function getBlockTypeName(): string
    {
        return t('Latest Sermons');
    }

    public function getBlockTypeDescription(): string
    {
        return t('Display recent sermon audio from Sermon Express entries or a Spotify podcast RSS feed.');
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
        $spotifyFeedUrl = trim((string) $this->spotifyFeedUrl);
        if ($sourceType === 'spotify' && $spotifyFeedUrl === '') {
            $spotifyFeedUrl = self::DEFAULT_SPOTIFY_FEED_URL;
        }
        $displayLimit = $this->getValidDisplayLimit((int) $this->displayLimit);
        $showDescriptions = isset($this->showDescriptions) ? (bool) $this->showDescriptions : true;
        $showPlayer = !empty($this->showPlayer);
        $showArchiveButton = !empty($this->showArchiveButton);
        $archiveButtonLabel = trim((string) $this->archiveButtonLabel)
            ?: ($sourceType === 'spotify' ? t('Listen on Spotify') : t('Latest Sermons'));
        $archiveButtonUrl = trim((string) $this->archiveButtonUrl);
        if ($sourceType === 'spotify' && $archiveButtonUrl === '') {
            $archiveButtonUrl = self::DEFAULT_SPOTIFY_SHOW_URL;
        }
        $sermons = $this->getSermons($sourceType, $displayLimit, $spotifyFeedUrl);
        $entity = $this->getSermonEntity();

        $this->set('title', $title);
        $this->set('intro', $intro);
        $this->set('sourceType', $sourceType);
        $this->set('spotifyFeedUrl', $spotifyFeedUrl);
        $this->set('displayLimit', $displayLimit);
        $this->set('showDescriptions', $showDescriptions);
        $this->set('showPlayer', $showPlayer);
        $this->set('showArchiveButton', $showArchiveButton && $archiveButtonUrl !== '');
        $this->set('archiveButtonLabel', $archiveButtonLabel);
        $this->set('archiveButtonUrl', $archiveButtonUrl);
        $this->set('entity', $entity);
        $this->set('sermons', $sermons);
        $this->set('emptyMessage', $this->feedErrorMessage ?: $this->getEmptyMessage($entity, $sourceType, $spotifyFeedUrl));
    }

    public function save($args): void
    {
        $args['title'] = trim((string) ($args['title'] ?? ''));
        $args['intro'] = trim((string) ($args['intro'] ?? ''));
        $args['sourceType'] = $this->getValidSourceType((string) ($args['sourceType'] ?? 'concrete_uploads'));
        $args['spotifyFeedUrl'] = trim((string) ($args['spotifyFeedUrl'] ?? ''));
        $args['displayLimit'] = $this->getValidDisplayLimit((int) ($args['displayLimit'] ?? 6));
        $args['showDescriptions'] = !empty($args['showDescriptions']) ? 1 : 0;
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
        $sourceType = $this->getValidSourceType((string) ($this->sourceType ?: 'concrete_uploads'));
        $spotifyFeedUrl = trim((string) ($this->spotifyFeedUrl ?? ''));
        if ($sourceType === 'spotify' && $spotifyFeedUrl === '') {
            $spotifyFeedUrl = self::DEFAULT_SPOTIFY_FEED_URL;
        }

        $this->set('spotifyFeedUrl', $spotifyFeedUrl);
        $this->set('defaultSpotifyShowUrl', self::DEFAULT_SPOTIFY_SHOW_URL);
        $this->set('defaultSpotifyFeedUrl', self::DEFAULT_SPOTIFY_FEED_URL);
        $this->set('displayLimit', $this->getValidDisplayLimit((int) ($this->displayLimit ?: 6)));
        $this->set('showDescriptions', isset($this->showDescriptions) ? (bool) $this->showDescriptions : true);
        $this->set('showPlayer', isset($this->showPlayer) ? (bool) $this->showPlayer : true);
        $this->set('showArchiveButton', isset($this->showArchiveButton) ? (bool) $this->showArchiveButton : true);
        $this->set('archiveButtonLabel', $this->archiveButtonLabel ?: ($sourceType === 'spotify' ? t('Listen on Spotify') : t('Latest Sermons')));
        $this->set('archiveButtonUrl', $this->archiveButtonUrl ?: ($sourceType === 'spotify' ? self::DEFAULT_SPOTIFY_SHOW_URL : '/resources/sermons'));
    }

    protected function getValidSourceType(string $sourceType): string
    {
        return in_array($sourceType, ['concrete_uploads', 'spotify'], true) ? $sourceType : 'concrete_uploads';
    }

    protected function getValidDisplayLimit(int $limit): int
    {
        return max(1, min($limit, 8));
    }

    protected function getSermonEntity(): ?Entity
    {
        $entityManager = app(EntityManagerInterface::class);

        return $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'sermon']);
    }

    protected function getSermons(string $sourceType, int $limit, string $spotifyFeedUrl = ''): array
    {
        if ($sourceType === 'spotify') {
            return $this->getSermonsFromSpotifyFeed($spotifyFeedUrl, $limit);
        }

        return $this->getSermonsFromConcreteUploads($limit);
    }

    protected function getSermonsFromSpotifyFeed(string $feedUrl, int $limit): array
    {
        $feedUrl = $this->getValidHttpUrl($feedUrl);
        if ($feedUrl === '') {
            return [];
        }
        if ($this->isSpotifyShowUrl($feedUrl)) {
            $this->feedErrorMessage = t('Use the Spotify for Creators RSS feed URL here. The public Spotify show link can be used for the Spotify button, but it does not expose episode audio.');

            return [];
        }

        if (isset(self::$spotifySermonsCache[$feedUrl])) {
            return array_slice(self::$spotifySermonsCache[$feedUrl], 0, $limit);
        }

        try {
            $feedService = app(FeedService::class);
            $feed = $feedService->load($feedUrl, $this->rssFeedCacheLifetime);
            $posts = $feedService->getPosts($feed);
        } catch (\Throwable $exception) {
            $this->feedErrorMessage = t('Unable to load the podcast feed. Check the RSS feed URL and try again.');

            return [];
        }

        $sermons = [];
        foreach ($posts as $index => $post) {
            $sermon = $this->buildSermonItemFromFeedPost($post, $index);
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

        self::$spotifySermonsCache[$feedUrl] = $sermons;

        return array_slice($sermons, 0, $limit);
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
            'duration_label' => '',
            'image_url' => '',
        ];
    }

    protected function buildSermonItemFromFeedPost($post, int $index): ?array
    {
        $enclosure = $this->callFeedMethod($post, 'getEnclosure');
        $streamUrl = '';
        $mimeType = '';

        if (is_object($enclosure)) {
            $streamUrl = $this->getValidHttpUrl((string) ($enclosure->url ?? $enclosure->href ?? ''));
            $mimeType = trim((string) ($enclosure->type ?? ''));
        }

        if ($streamUrl === '' || !$this->isAudioUrl($streamUrl, $mimeType)) {
            return null;
        }

        $title = $this->cleanFeedText((string) $this->callFeedMethod($post, 'getTitle'));
        if ($title === '') {
            $title = t('Untitled sermon');
        }

        $speaker = $this->cleanFeedText((string) $this->callFeedMethod($post, 'getCastAuthor'));
        $rawDescription = $this->getFeedDescription($post);
        $description = $this->cleanFeedText($rawDescription);
        $descriptionHtml = $this->cleanFeedHtml($rawDescription);
        if ($speaker === '') {
            $speaker = $this->extractSpeakerFromDescription($description);
        }

        $dateLabel = '';
        $preachedAtTimestamp = 0;
        $date = $this->callFeedMethod($post, 'getDateCreated');
        if (!$date instanceof \DateTimeInterface) {
            $date = $this->callFeedMethod($post, 'getDateModified');
        }
        if ($date instanceof \DateTimeInterface) {
            $preachedAtTimestamp = $date->getTimestamp();
            $dateLabel = $date->format('j M Y');
        }

        $durationLabel = $this->formatDurationLabel((string) $this->callFeedMethod($post, 'getDuration'));
        $imageUrl = $this->getFeedImageUrl($post);
        $sourceId = $this->cleanFeedText((string) $this->callFeedMethod($post, 'getId'));
        if ($sourceId === '') {
            $sourceId = $this->cleanFeedText((string) $this->callFeedMethod($post, 'getLink'));
        }
        if ($sourceId === '') {
            $sourceId = $streamUrl . '#' . $index;
        }

        return [
            'id' => 'spotify-' . sha1($sourceId),
            'title' => $title,
            'speaker' => $speaker,
            'description' => $description,
            'description_html' => $descriptionHtml,
            'date_label' => $dateLabel,
            'preached_at_timestamp' => $preachedAtTimestamp,
            'stream_url' => $streamUrl,
            'download_url' => $streamUrl,
            'duration_label' => $durationLabel,
            'image_url' => $imageUrl,
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

    protected function callFeedMethod($feedObject, string $method)
    {
        try {
            return $feedObject->$method();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function getFeedDescription($post): string
    {
        foreach (['getSummary', 'getDescription', 'getContent'] as $method) {
            $description = (string) $this->callFeedMethod($post, $method);
            if (trim($description) !== '') {
                return $description;
            }
        }

        return '';
    }

    protected function getFeedImageUrl($post): string
    {
        foreach (['getItunesImage'] as $method) {
            $imageUrl = $this->getValidHttpUrl((string) $this->callFeedMethod($post, $method));
            if ($imageUrl !== '') {
                return $imageUrl;
            }
        }

        return '';
    }

    protected function getValidHttpUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : '';
    }

    protected function isAudioUrl(string $url, string $mimeType = ''): bool
    {
        $mimeType = strtolower(trim($mimeType));
        if ($mimeType !== '' && strpos($mimeType, 'audio/') === 0) {
            return true;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);

        return (bool) preg_match('/\.(mp3|m4a|aac|wav|ogg|oga|flac)(?:$|\?)/i', $path);
    }

    protected function isSpotifyShowUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        return in_array($host, ['open.spotify.com', 'play.spotify.com'], true)
            && preg_match('~/show/[A-Za-z0-9]+~', $path) === 1;
    }

    protected function cleanFeedText(string $value): string
    {
        $value = $this->decodeFeedHtml($value);
        $value = preg_replace('~<\s*(script|style)\b[^>]*>.*?</\s*\1\s*>~is', ' ', $value) ?: $value;
        $value = preg_replace('~<\s*br\s*/?\s*>~i', ' ', $value) ?: $value;
        $value = preg_replace('~</\s*(p|div|li|h[1-6]|blockquote)\s*>~i', ' ', $value) ?: $value;
        $value = strip_tags($value);
        $value = str_replace("\xc2\xa0", ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?: $value;

        return trim($value);
    }

    protected function cleanFeedHtml(string $value): string
    {
        $value = $this->decodeFeedHtml($value);
        if ($value === '') {
            return '';
        }

        $html = $this->sanitizeFeedHtml($value);
        $html = preg_replace('~<p>\s*(?:<br>)?\s*</p>~i', '', $html) ?: $html;

        return trim($html);
    }

    protected function decodeFeedHtml(string $value): string
    {
        $value = trim($value);

        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $value) {
                break;
            }

            $value = $decoded;
        }

        return trim($value);
    }

    protected function sanitizeFeedHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousErrorSetting = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        if (!$loaded) {
            return htmlspecialchars(strip_tags($html), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $wrapper = $document->getElementsByTagName('div')->item(0);
        if (!$wrapper instanceof \DOMElement) {
            return '';
        }

        $output = '';
        foreach ($wrapper->childNodes as $childNode) {
            $output .= $this->sanitizeFeedHtmlNode($childNode);
        }

        return $output;
    }

    protected function sanitizeFeedHtmlNode(\DOMNode $node): string
    {
        if ($node instanceof \DOMText) {
            return htmlspecialchars($node->nodeValue ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        if (!$node instanceof \DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);
        if (in_array($tag, ['embed', 'iframe', 'math', 'object', 'script', 'style', 'svg'], true)) {
            return '';
        }

        $allowedTags = ['a', 'b', 'br', 'em', 'i', 'li', 'ol', 'p', 'strong', 'u', 'ul'];
        $children = '';

        foreach ($node->childNodes as $childNode) {
            $children .= $this->sanitizeFeedHtmlNode($childNode);
        }

        if (!in_array($tag, $allowedTags, true)) {
            return $children;
        }

        if ($tag === 'br') {
            return '<br>';
        }

        $attributes = '';
        if ($tag === 'a') {
            $href = $this->getValidHttpUrl((string) $node->getAttribute('href'));
            if ($href !== '') {
                $attributes .= ' href="' . htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
                $attributes .= ' rel="noopener noreferrer"';
            }

            $title = trim((string) $node->getAttribute('title'));
            if ($title !== '') {
                $attributes .= ' title="' . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
            }
        }

        return '<' . $tag . $attributes . '>' . $children . '</' . $tag . '>';
    }

    protected function shortenText(string $value, int $limit): string
    {
        if ($value === '' || $limit < 1) {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($value) <= $limit) {
                return $value;
            }

            return rtrim(mb_substr($value, 0, $limit), " \t\n\r\0\x0B,.;:-") . '...';
        }

        if (strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(substr($value, 0, $limit), " \t\n\r\0\x0B,.;:-") . '...';
    }

    protected function extractSpeakerFromDescription(string $description): string
    {
        if (!preg_match('/(?:^|\s)(?:Guest\s+Speaker|Speaker):\s*(.+)$/i', $description, $matches)) {
            return '';
        }

        return $this->shortenText(trim((string) $matches[1]), 90);
    }

    protected function formatDurationLabel(string $duration): string
    {
        $duration = trim($duration);
        if ($duration === '') {
            return '';
        }

        if (ctype_digit($duration)) {
            $seconds = (int) $duration;
            $hours = (int) floor($seconds / 3600);
            $minutes = (int) floor(($seconds % 3600) / 60);
            $remainingSeconds = $seconds % 60;

            return $hours > 0
                ? sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds)
                : sprintf('%d:%02d', $minutes, $remainingSeconds);
        }

        return $duration;
    }

    protected function getEmptyMessage(?Entity $entity, string $sourceType, string $spotifyFeedUrl): string
    {
        if ($sourceType === 'spotify') {
            if ($this->getValidHttpUrl($spotifyFeedUrl) === '') {
                return t('Add the Spotify podcast RSS feed URL in this block to begin showing sermons here.');
            }

            return t('No playable podcast episodes were found in the Spotify RSS feed.');
        }

        if ($entity) {
            return t(
                'Add sermon entries to the %s Express entity to populate this section.',
                $entity->getName()
            );
        }

        return t('Create the Sermon Express entity to begin showing sermons here.');
    }
}
