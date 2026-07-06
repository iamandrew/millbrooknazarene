<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$site = \Core::make('site')->getSite();
$page = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find the home page.</error>');
    return 1;
}

$blockType = BlockType::getByHandle('whats_on_block');
if (!$blockType) {
    $blockType = BlockType::installBlockType('whats_on_block');
    $output->writeln('<info>Installed block type: whats_on_block</info>');
}

if (!$blockType) {
    $output->writeln('<error>Could not install or load the What’s On block type.</error>');
    return 1;
}

$area = Area::getOrCreate($page, 'Home Visit Card');
$existingBlocks = $area->getAreaBlocksArray($page);

$defaultTitle = 'A few ways to begin.';
$legacyTitles = [
    'A few simple ways to connect through the month.',
    'Ways to connect this month.',
    'Ways to connect.',
];
$defaultIntro = 'If you are wondering where to start, Sunday worship is always a good first step. These regular rhythms help people pray, connect, and grow together.';
$legacyIntros = [
    'Alongside Sunday worship, there are regular gatherings, groups, and church rhythms that help people pray, connect, and grow together.',
    'Regular rhythms across the week help people pray, connect, and grow together.',
    'Community gatherings, family activities, and regular rhythms help people find a place to belong.',
];
$defaultSecondaryButtonLabel = 'Listen to recent teaching';
$defaultSecondaryButtonUrl = '/resources/sermons';
$legacySecondaryLabels = [
    'Latest Sermons',
];
$legacySecondaryUrls = [
    '/resources/sermons',
];

$blockData = [
    'title' => $defaultTitle,
    'intro' => $defaultIntro,
    'layout' => 'compact',
    'itemsJson' => json_encode([
        [
            'eyebrow' => 'Sunday',
            'title' => 'Worship at 11:00am',
            'summary' => 'Join us each Sunday for worship, prayer, Bible teaching, and time together afterwards.',
            'linkLabel' => 'Plan your visit',
            'linkUrl' => '/visit-us',
        ],
        [
            'eyebrow' => 'Prayer',
            'title' => 'Prayer before church at 10:45am',
            'summary' => 'You are welcome to join the short prayer time before the Sunday service.',
            'linkLabel' => 'Plan your visit',
            'linkUrl' => '/visit-us',
        ],
        [
            'eyebrow' => 'Youth',
            'title' => 'Sunday evenings for young people',
            'summary' => 'A relaxed space for secondary school age young people, with snacks, games, teaching, trips, and time together.',
            'linkLabel' => 'Youth',
            'linkUrl' => '/community/youth',
        ],
        [
            'eyebrow' => 'Homegroups',
            'title' => 'Sunday and Wednesday evenings in homes',
            'summary' => 'Smaller spaces for friendship, Bible discussion, support, and prayer.',
            'linkLabel' => 'Homegroups',
            'linkUrl' => '/community/homegroups',
        ],
        [
            'eyebrow' => 'Community',
            'title' => 'Community Cafe, Cafe Fit, and local events',
            'summary' => 'Simple spaces through the week for connection, wellbeing, friendship, and support.',
            'linkLabel' => 'See What\'s On',
            'linkUrl' => '/community/whats-on',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'primaryButtonLabel' => 'See What’s On',
    'primaryButtonUrl' => '/community/whats-on',
    'secondaryButtonLabel' => $defaultSecondaryButtonLabel,
    'secondaryButtonUrl' => $defaultSecondaryButtonUrl,
];

foreach ($existingBlocks as $block) {
    if ($block->getBlockTypeHandle() === 'whats_on_block') {
        $controller = $block->getController();
        $currentTitle = trim((string) ($controller->title ?? ''));
        $currentIntro = trim((string) ($controller->intro ?? ''));
        $currentSecondaryLabel = trim((string) ($controller->secondaryButtonLabel ?? ''));
        $currentSecondaryUrl = trim((string) ($controller->secondaryButtonUrl ?? ''));
        $currentItemsJson = trim((string) ($controller->itemsJson ?? ''));
        $shouldRefreshItems = $currentItemsJson === ''
            || str_contains($currentItemsJson, 'Recent teaching')
            || !str_contains($currentItemsJson, 'Homegroups')
            || !str_contains($currentItemsJson, 'Sunday and Wednesday evenings in homes')
            || !str_contains($currentItemsJson, 'Community Cafe')
            || str_contains($currentItemsJson, 'Latest sermons');
        $blockData = [
            'title' => in_array($currentTitle, $legacyTitles, true) ? $defaultTitle : ($currentTitle ?: $blockData['title']),
            'intro' => in_array($currentIntro, $legacyIntros, true) ? $defaultIntro : ($currentIntro ?: $blockData['intro']),
            'layout' => 'compact',
            'itemsJson' => $shouldRefreshItems ? $blockData['itemsJson'] : $currentItemsJson,
            'primaryButtonLabel' => trim((string) ($controller->primaryButtonLabel ?? $blockData['primaryButtonLabel'])),
            'primaryButtonUrl' => trim((string) ($controller->primaryButtonUrl ?? $blockData['primaryButtonUrl'])),
            'secondaryButtonLabel' => $currentSecondaryLabel === '' || in_array($currentSecondaryLabel, $legacySecondaryLabels, true) ? $defaultSecondaryButtonLabel : $currentSecondaryLabel,
            'secondaryButtonUrl' => $currentSecondaryUrl === '' || in_array($currentSecondaryUrl, $legacySecondaryUrls, true) ? $defaultSecondaryButtonUrl : $currentSecondaryUrl,
        ];
        break;
    }
}

foreach ($existingBlocks as $block) {
    $block->deleteBlock();
}

$page->addBlock($blockType, $area, $blockData);

$output->writeln('<info>Rebuilt the homepage What’s On slot with the compact What’s On block.</info>');

return 0;
