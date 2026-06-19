<?php

namespace Application\Block\WhatsOnBlock;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
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
        $this->set('layout', $layout);
        $this->set('items', $items);
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
        $this->set('title', $this->title ?: t('A few simple ways to connect through the month.'));
        $this->set('intro', $this->intro ?: t('Alongside Sunday worship, there are regular gatherings, groups, and church rhythms that help people pray, connect, and grow together.'));
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
            if (!$entry instanceof Entry) {
                continue;
            }

            $title = trim((string) $entry->getAttribute('item_title'));
            $summary = trim((string) $entry->getAttribute('summary'));
            $eyebrow = trim((string) $entry->getAttribute('eyebrow'));
            $linkLabel = trim((string) $entry->getAttribute('link_label'));
            $linkUrl = trim((string) $entry->getAttribute('link_url'));

            if ($title === '' && $summary === '') {
                continue;
            }

            $items[] = [
                'eyebrow' => $eyebrow,
                'title' => $title,
                'summary' => $summary,
                'linkLabel' => $linkLabel,
                'linkUrl' => $linkUrl,
            ];
        }

        return $items;
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
                'linkLabel' => $linkLabel,
                'linkUrl' => $linkUrl,
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
                'linkLabel' => 'Plan your visit',
                'linkUrl' => '/visit-us',
            ],
            [
                'eyebrow' => 'Midweek',
                'title' => 'Homegroups, prayer, and shared life',
                'summary' => 'Smaller gatherings through the week help people build friendships, pray together, and keep growing in faith.',
                'linkLabel' => 'Explore church life',
                'linkUrl' => '/community',
            ],
            [
                'eyebrow' => 'Families',
                'title' => 'Children and families are welcome',
                'summary' => 'Children are a valued part of church life, with support for families and age-appropriate opportunities to belong.',
                'linkLabel' => 'Children & families',
                'linkUrl' => '/community/children',
            ],
            [
                'eyebrow' => 'Recent teaching',
                'title' => 'Catch up on sermons and Bible teaching',
                'summary' => 'Listen back to recent messages from Millbrook before you visit or during the week.',
                'linkLabel' => 'Listen to latest sermons',
                'linkUrl' => '/resources/sermons',
            ],
        ];
    }

    protected function getValidLayout(string $layout): string
    {
        return in_array($layout, ['cards', 'compact'], true) ? $layout : 'cards';
    }
}
