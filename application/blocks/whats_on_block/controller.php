<?php

namespace Application\Block\WhatsOnBlock;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected const SECTION_CONFIG = [
        'weekly' => [
            'kicker' => 'Weekly',
            'title' => 'Weekly rhythm.',
            'summary' => 'Some gatherings are open to everyone. Others are for a particular age or stage, but you are always welcome to ask where to start.',
            'cardsClass' => 'weekly',
            'order' => 20,
        ],
        'monthly' => [
            'kicker' => 'Monthly',
            'title' => 'Monthly rhythm.',
            'summary' => 'Monthly gatherings are a good way to get to know people at an easier pace.',
            'cardsClass' => 'monthly',
            'order' => 30,
        ],
        'seasonal' => [
            'kicker' => 'Seasonal',
            'title' => 'Seasonal moments.',
            'summary' => 'These are advertised when dates are confirmed, so it is worth checking before you come.',
            'cardsClass' => 'seasonal',
            'order' => 40,
        ],
        'special' => [
            'kicker' => 'Upcoming',
            'title' => 'Upcoming',
            'summary' => 'Current one-off gatherings and sign-ups will appear here when there is something to highlight.',
            'cardsClass' => 'special',
            'order' => 10,
        ],
    ];

    protected $btTable = 'btWhatsOnBlock';
    protected $btInterfaceWidth = 760;
    protected $btInterfaceHeight = 620;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public function getBlockTypeName(): string
    {
        return t('What’s On');
    }

    public function getBlockTypeDescription(): string
    {
        return t('Display shared What’s On items from Express in a visitor-friendly layout.');
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
        $layout = $this->getValidLayout((string) $this->layout);
        $items = $this->getItems();
        $primaryButtonLabel = trim((string) $this->primaryButtonLabel);
        $primaryButtonUrl = trim((string) $this->primaryButtonUrl);
        $secondaryButtonLabel = trim((string) $this->secondaryButtonLabel);
        $secondaryButtonUrl = trim((string) $this->secondaryButtonUrl);

        $this->set('title', $title);
        $this->set('intro', $intro);
        $this->set('introParagraphs', $this->getIntroParagraphs($intro));
        $this->set('layout', $layout);
        $this->set('items', $items);
        $this->set('groupedItems', $layout === 'cards' ? $this->getGroupedItems($items) : []);
        $this->set('sectionConfig', self::SECTION_CONFIG);
        $this->set('primaryButtonLabel', $primaryButtonLabel);
        $this->set('primaryButtonUrl', $primaryButtonUrl);
        $this->set('secondaryButtonLabel', $secondaryButtonLabel);
        $this->set('secondaryButtonUrl', $secondaryButtonUrl);
        $this->set('hasPrimaryButton', $primaryButtonLabel !== '' && $primaryButtonUrl !== '');
        $this->set('hasSecondaryButton', $secondaryButtonLabel !== '' && $secondaryButtonUrl !== '');
        $this->set('hasSharedSource', $this->getWhatsOnEntity() instanceof Entity);
    }

    public function save($args): void
    {
        $args['title'] = trim((string) ($args['title'] ?? ''));
        $args['intro'] = trim((string) ($args['intro'] ?? ''));
        $args['layout'] = $this->getValidLayout((string) ($args['layout'] ?? 'cards'));
        $args['primaryButtonLabel'] = trim((string) ($args['primaryButtonLabel'] ?? ''));
        $args['primaryButtonUrl'] = trim((string) ($args['primaryButtonUrl'] ?? ''));
        $args['secondaryButtonLabel'] = trim((string) ($args['secondaryButtonLabel'] ?? ''));
        $args['secondaryButtonUrl'] = trim((string) ($args['secondaryButtonUrl'] ?? ''));

        parent::save($args);
    }

    protected function setDefaults(): void
    {
        $this->set('title', $this->title ?: t('A simple guide to what happens at Millbrook.'));
        $this->set('intro', $this->intro ?: t("Church life has a regular rhythm, but it is not always the same every week. This page gives you the shape of what usually happens, rather than a live events calendar.\n\nIf you are new, Sunday morning is always a good place to begin. For one-off events, current dates, or booking details, check social media, the newsletter, or get in touch."));
        $this->set('layout', $this->getValidLayout((string) ($this->layout ?: 'cards')));
        $this->set('items', $this->getItems());
        $this->set('primaryButtonLabel', $this->primaryButtonLabel ?: t('Visit Us?'));
        $this->set('primaryButtonUrl', $this->primaryButtonUrl ?: '/visit-us');
        $this->set('secondaryButtonLabel', $this->secondaryButtonLabel ?: t('Latest Sermons'));
        $this->set('secondaryButtonUrl', $this->secondaryButtonUrl ?: '/resources/sermons');
        $this->set('hasSharedSource', $this->getWhatsOnEntity() instanceof Entity);
    }

    protected function getItems(): array
    {
        $layout = $this->getValidLayout((string) $this->layout);
        $legacyJson = trim((string) ($this->itemsJson ?? ''));

        if ($layout === 'compact' && $legacyJson !== '') {
            return array_slice($this->getLegacyItems(), 0, 4);
        }

        $items = $this->getItemsFromExpress();
        if ($items !== []) {
            if ($layout === 'compact') {
                return array_slice($items, 0, 4);
            }

            return $items;
        }

        return $this->getLegacyItems();
    }

    protected function getItemsFromExpress(): array
    {
        $entity = $this->getWhatsOnEntity();
        if (!$entity) {
            return [];
        }

        $list = new EntryList($entity);
        $list->ignorePermissions();

        $items = [];
        foreach ($list->getResults() as $entry) {
            if (!$entry instanceof Entry || !$this->isEntryVisible($entry)) {
                continue;
            }

            $title = trim((string) $entry->getAttribute('item_title'));
            $summary = trim((string) $entry->getAttribute('summary'));
            $eyebrow = trim((string) $entry->getAttribute('eyebrow'));
            $meta = trim((string) $entry->getAttribute('meta'));
            $details = $this->getDetailRows([
                'When' => trim((string) $entry->getAttribute('detail_when')),
                'Where' => trim((string) $entry->getAttribute('detail_where')),
                'For' => trim((string) $entry->getAttribute('detail_for')),
                'First time?' => trim((string) $entry->getAttribute('detail_first_time')),
            ]);
            $linkLabel = trim((string) $entry->getAttribute('link_label'));
            $linkUrl = trim((string) $entry->getAttribute('link_url'));
            $section = $this->getValidSection((string) $entry->getAttribute('section'));
            $cardStyle = $this->getValidCardStyle((string) $entry->getAttribute('card_style'));
            $sortOrder = (int) $entry->getAttribute('sort_order');

            if ($title === '' && $summary === '') {
                continue;
            }

            $items[] = [
                'eyebrow' => $eyebrow,
                'title' => $title,
                'summary' => $summary,
                'meta' => $meta,
                'details' => $details,
                'linkLabel' => $linkLabel,
                'linkUrl' => $linkUrl,
                'section' => $section,
                'cardStyle' => $cardStyle,
                'sortOrder' => $sortOrder,
                'displayOrder' => (int) $entry->getEntryDisplayOrder(),
            ];
        }

        usort($items, function (array $a, array $b): int {
            $sectionCompare = (self::SECTION_CONFIG[$a['section']]['order'] ?? 999)
                <=> (self::SECTION_CONFIG[$b['section']]['order'] ?? 999);

            if ($sectionCompare !== 0) {
                return $sectionCompare;
            }

            $sortCompare = ($a['sortOrder'] ?: $a['displayOrder']) <=> ($b['sortOrder'] ?: $b['displayOrder']);

            if ($sortCompare !== 0) {
                return $sortCompare;
            }

            return strcasecmp($a['title'], $b['title']);
        });

        return $items;
    }

    protected function getDetailRows(array $details): array
    {
        $rows = [];

        foreach ($details as $label => $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            $rows[] = [
                'label' => (string) $label,
                'value' => $value,
            ];
        }

        return $rows;
    }

    protected function isEntryVisible(Entry $entry): bool
    {
        if ((bool) $entry->getAttribute('always_on')) {
            return true;
        }

        $start = $this->normaliseDate($entry->getAttribute('start_date'));
        $end = $this->normaliseDate($entry->getAttribute('end_date'));

        if (!$start && !$end) {
            return false;
        }

        $now = new \DateTimeImmutable('now');

        if ($start && $now < $start) {
            return false;
        }

        if ($end) {
            $endTime = $end;
            if ($endTime->format('H:i:s') === '00:00:00') {
                $endTime = $endTime->setTime(23, 59, 59);
            }

            if ($now > $endTime) {
                return false;
            }
        }

        return true;
    }

    protected function normaliseDate($value): ?\DateTimeImmutable
    {
        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($value);
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function getGroupedItems(array $items): array
    {
        $groups = [];

        foreach ($items as $item) {
            $section = $this->getValidSection((string) ($item['section'] ?? 'weekly'));
            $groups[$section][] = $item;
        }

        uksort($groups, function (string $a, string $b): int {
            return (self::SECTION_CONFIG[$a]['order'] ?? 999) <=> (self::SECTION_CONFIG[$b]['order'] ?? 999);
        });

        return $groups;
    }

    protected function getIntroParagraphs(string $intro): array
    {
        $intro = trim($intro);
        if ($intro === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $intro))));
    }

    protected function getLegacyItems(): array
    {
        $json = trim((string) ($this->itemsJson ?? ''));
        if ($json === '') {
            return $this->getDefaultItems();
        }

        $items = json_decode($json, true);
        if (!is_array($items)) {
            return $this->getDefaultItems();
        }

        $items = $this->sanitizeItems($items);

        return $items !== [] ? $items : $this->getDefaultItems();
    }

    protected function getWhatsOnEntity(): ?Entity
    {
        $entityManager = app(EntityManagerInterface::class);

        return $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'whats_on_item']);
    }

    protected function sanitizeItems(array $items): array
    {
        $clean = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $eyebrow = trim((string) ($item['eyebrow'] ?? ''));
            $title = trim((string) ($item['title'] ?? ''));
            $summary = trim((string) ($item['summary'] ?? ''));
            $linkLabel = trim((string) ($item['linkLabel'] ?? ''));
            $linkUrl = trim((string) ($item['linkUrl'] ?? ''));

            if ($title === '' && $summary === '') {
                continue;
            }

            $clean[] = [
                'eyebrow' => $eyebrow,
                'title' => $title,
                'summary' => $summary,
                'meta' => '',
                'linkLabel' => $linkLabel,
                'linkUrl' => $linkUrl,
                'section' => 'weekly',
                'cardStyle' => 'blue',
                'sortOrder' => 0,
                'displayOrder' => count($clean),
            ];
        }

        return array_slice($clean, 0, 12);
    }

    protected function getDefaultItems(): array
    {
        return [
            [
                'eyebrow' => 'Sunday',
                'title' => 'Worship at 11:00am',
                'summary' => 'A welcoming Sunday gathering with worship, prayer, Bible teaching, and time together afterwards.',
                'meta' => '',
                'linkLabel' => 'Plan your visit',
                'linkUrl' => '/visit-us',
                'section' => 'weekly',
                'cardStyle' => 'blue',
                'sortOrder' => 10,
                'displayOrder' => 10,
            ],
            [
                'eyebrow' => 'Midweek',
                'title' => 'Homegroups, prayer, and shared life',
                'summary' => 'Smaller gatherings through the week help people build friendships, pray together, and keep growing in faith.',
                'meta' => '',
                'linkLabel' => 'Explore church life',
                'linkUrl' => '/community',
                'section' => 'weekly',
                'cardStyle' => 'blue',
                'sortOrder' => 20,
                'displayOrder' => 20,
            ],
            [
                'eyebrow' => 'Families',
                'title' => 'Children and families are welcome',
                'summary' => 'Children are a valued part of church life, with support for families and age-appropriate opportunities to belong.',
                'meta' => '',
                'linkLabel' => 'Children & families',
                'linkUrl' => '/community/children',
                'section' => 'weekly',
                'cardStyle' => 'lime',
                'sortOrder' => 30,
                'displayOrder' => 30,
            ],
            [
                'eyebrow' => 'Recent teaching',
                'title' => 'Catch up on sermons and Bible teaching',
                'summary' => 'Listen back to recent messages from Millbrook before you visit or during the week.',
                'meta' => '',
                'linkLabel' => 'Listen to latest sermons',
                'linkUrl' => '/resources/sermons',
                'section' => 'weekly',
                'cardStyle' => 'purple',
                'sortOrder' => 40,
                'displayOrder' => 40,
            ],
        ];
    }

    protected function getValidLayout(string $layout): string
    {
        return in_array($layout, ['cards', 'compact'], true) ? $layout : 'cards';
    }

    protected function getValidSection(string $section): string
    {
        $section = strtolower(trim($section));

        return array_key_exists($section, self::SECTION_CONFIG) ? $section : 'weekly';
    }

    protected function getValidCardStyle(string $cardStyle): string
    {
        $cardStyle = strtolower(trim($cardStyle));

        return in_array($cardStyle, ['blue', 'lime', 'purple', 'coral', 'dark'], true) ? $cardStyle : 'blue';
    }
}
