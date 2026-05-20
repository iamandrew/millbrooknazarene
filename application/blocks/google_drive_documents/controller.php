<?php

namespace Application\Block\GoogleDriveDocuments;

use Concrete\Core\Block\BlockController;
use RuntimeException;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected $btTable = 'btGoogleDriveDocuments';
    protected $btInterfaceWidth = 700;
    protected $btInterfaceHeight = 520;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    protected static array $documentsCache = [];

    public function getBlockTypeName(): string
    {
        return t('Google Drive Documents');
    }

    public function getBlockTypeDescription(): string
    {
        return t('Display a public Google Drive folder of church documents.');
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
        $folderUrl = trim((string) $this->folderUrl);
        $folderId = $this->extractFolderId($folderUrl);

        $documents = [];
        $documentsError = '';

        if ($folderId !== '') {
            try {
                $documents = $this->fetchFolderDocuments($folderId);
            } catch (\Throwable $exception) {
                $documentsError = t('We could not load the document library right now. Please try again shortly.');
            }
        }

        $this->set('title', $title);
        $this->set('intro', $intro);
        $this->set('folderUrl', $folderUrl);
        $this->set('folderId', $folderId);
        $this->set('buttonLabel', trim((string) $this->buttonLabel) ?: t('Open full folder'));
        $this->set('showButton', (bool) $this->showButton);
        $this->set('viewMode', $this->getValidViewMode((string) $this->viewMode));
        $this->set('embedHeight', $this->getValidEmbedHeight((int) $this->embedHeight));
        $this->set('documents', $documents);
        $this->set('documentsError', $documentsError);
    }

    public function save($args): void
    {
        $args['title'] = trim((string) ($args['title'] ?? ''));
        $args['intro'] = trim((string) ($args['intro'] ?? ''));
        $args['folderUrl'] = trim((string) ($args['folderUrl'] ?? ''));
        $args['buttonLabel'] = trim((string) ($args['buttonLabel'] ?? ''));
        $args['showButton'] = !empty($args['showButton']) ? 1 : 0;
        $args['viewMode'] = $this->getValidViewMode((string) ($args['viewMode'] ?? 'list'));
        $args['embedHeight'] = $this->getValidEmbedHeight((int) ($args['embedHeight'] ?? 640));

        parent::save($args);
    }

    protected function setDefaults(): void
    {
        $this->set('title', $this->title ?: t('Church documents'));
        $this->set('intro', $this->intro ?: '');
        $this->set('folderUrl', $this->folderUrl ?: '');
        $this->set('buttonLabel', $this->buttonLabel ?: t('Open full folder'));
        $this->set('showButton', isset($this->showButton) ? (bool) $this->showButton : true);
        $this->set('viewMode', $this->getValidViewMode((string) ($this->viewMode ?: 'list')));
        $this->set('embedHeight', $this->getValidEmbedHeight((int) ($this->embedHeight ?: 640)));
    }

    protected function getValidViewMode(string $viewMode): string
    {
        return in_array($viewMode, ['list', 'grid'], true) ? $viewMode : 'list';
    }

    protected function getValidEmbedHeight(int $height): int
    {
        return max(360, min($height, 1200));
    }

    protected function extractFolderId(string $folderUrl): string
    {
        $folderUrl = trim($folderUrl);
        if ($folderUrl === '') {
            return '';
        }

        if (preg_match('~(?:/folders/|[?&]id=)([-_a-zA-Z0-9]+)~', $folderUrl, $matches)) {
            return $matches[1];
        }

        if (preg_match('~^[-_a-zA-Z0-9]{20,}$~', $folderUrl)) {
            return $folderUrl;
        }

        return '';
    }

    protected function fetchFolderDocuments(string $folderId): array
    {
        if (isset(self::$documentsCache[$folderId])) {
            return self::$documentsCache[$folderId];
        }

        $html = $this->fetchFolderHtml($folderId);
        $documents = $this->parseFolderDocumentsFromHtml($html);
        self::$documentsCache[$folderId] = $documents;

        return $documents;
    }

    protected function fetchFolderHtml(string $folderId): string
    {
        $url = 'https://drive.google.com/drive/folders/' . rawurlencode($folderId);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 6,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_USERAGENT => 'Millbrook Policies Block/1.0',
            ]);

            $response = curl_exec($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if (is_string($response) && $response !== '' && $statusCode >= 200 && $statusCode < 400) {
                return $response;
            }

            if ($error !== '') {
                throw new RuntimeException($error);
            }
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 12,
                'header' => "User-Agent: Millbrook Policies Block/1.0\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if (!is_string($response) || $response === '') {
            throw new RuntimeException('Unable to fetch the Google Drive folder.');
        }

        return $response;
    }

    protected function parseFolderDocumentsFromHtml(string $html): array
    {
        $marker = "window['_DRIVE_ivd'] = '";
        $start = strpos($html, $marker);
        if ($start === false) {
            return [];
        }

        $start += strlen($marker);
        $end = strpos($html, "';if (window['_DRIVE_ivdc'])", $start);
        if ($end === false) {
            return [];
        }

        $raw = substr($html, $start, $end - $start);
        $decoded = stripcslashes($raw);
        $data = json_decode($decoded, true);

        if (!is_array($data) || !isset($data[0]) || !is_array($data[0])) {
            return [];
        }

        $documents = [];

        foreach ($data[0] as $file) {
            if (!is_array($file) || !isset($file[0], $file[2])) {
                continue;
            }

            $documents[] = $this->buildDocumentItem($file);
        }

        usort(
            $documents,
            static function (array $a, array $b): int {
                if ($a['modified_at'] === $b['modified_at']) {
                    return strcmp($a['display_name'], $b['display_name']);
                }

                return $b['modified_at'] <=> $a['modified_at'];
            }
        );

        return $documents;
    }

    protected function buildDocumentItem(array $file): array
    {
        $name = trim((string) ($file[2] ?? 'Untitled document'));
        $extension = strtoupper((string) ($file[44] ?? pathinfo($name, PATHINFO_EXTENSION)));
        $modifiedAt = (int) (($file[10] ?? 0) ?: ($file[9] ?? 0));
        $sizeBytes = (int) (($file[13] ?? 0) ?: ($file[27] ?? 0));
        $viewUrl = (string) ($file[114] ?? '');

        if ($viewUrl === '') {
            $viewUrl = 'https://drive.google.com/file/d/' . rawurlencode((string) $file[0]) . '/view';
        }

        return [
            'id' => (string) $file[0],
            'name' => $name,
            'display_name' => $this->formatDocumentTitle($name),
            'extension' => $extension !== '' ? $extension : strtoupper((string) ($file[3] ?? 'FILE')),
            'mime' => (string) ($file[3] ?? ''),
            'view_url' => $viewUrl,
            'download_url' => 'https://drive.google.com/uc?export=download&id=' . rawurlencode((string) $file[0]),
            'modified_at' => $modifiedAt,
            'modified_label' => $modifiedAt > 0 ? date('j M Y', (int) floor($modifiedAt / 1000)) : '',
            'size_bytes' => $sizeBytes,
            'size_label' => $sizeBytes > 0 ? $this->formatBytes($sizeBytes) : '',
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $value = $bytes / 1024;

        foreach ($units as $index => $unit) {
            if ($value < 1024 || $index === count($units) - 1) {
                return rtrim(rtrim(number_format($value, $value >= 10 ? 0 : 1, '.', ''), '0'), '.') . ' ' . $unit;
            }

            $value /= 1024;
        }

        return $bytes . ' B';
    }

    protected function formatDocumentTitle(string $name): string
    {
        $displayName = trim($name);

        for ($i = 0; $i < 2; $i++) {
            $updated = preg_replace('/\.([A-Za-z0-9]{2,5})$/', '', $displayName);
            if (!is_string($updated) || $updated === $displayName) {
                break;
            }
            $displayName = $updated;
        }

        $displayName = str_replace('_', ' ', $displayName);
        $displayName = preg_replace('/\s+/', ' ', $displayName) ?: $displayName;
        $displayName = preg_replace('/\s+([,.;:!?])/', '$1', $displayName) ?: $displayName;
        $displayName = preg_replace('/[!\-_.\s]+$/', '', $displayName) ?: $displayName;

        return trim($displayName) !== '' ? trim($displayName) : $name;
    }
}
