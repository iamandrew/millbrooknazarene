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

$defaultTitle = 'Ways to connect.';
$legacyTitles = [
    'A few simple ways to connect through the month.',
    'Ways to connect this month.',
];
$defaultIntro = 'Community gatherings, family activities, and regular rhythms help people find a place to belong.';
$legacyIntros = [
    'Alongside Sunday worship, there are regular gatherings, groups, and church rhythms that help people pray, connect, and grow together.',
];
$defaultSecondaryButtonLabel = '';
$defaultSecondaryButtonUrl = '';
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
            'eyebrow' => 'Community',
            'title' => 'Community Cafe and Cafe Fit',
            'summary' => 'Spaces through the week for connection, wellbeing, friendship, and support.',
            'linkLabel' => 'See What\'s On',
            'linkUrl' => '/community/whats-on',
        ],
        [
            'eyebrow' => 'Families',
            'title' => 'Kids activities and family life',
            'summary' => 'Sunday School, seasonal children\'s activities, and Kids Summer Club help families connect.',
            'linkLabel' => 'Children & families',
            'linkUrl' => '/community/children',
        ],
        [
            'eyebrow' => 'Sunday',
            'title' => 'Sunday worship and First Breakfast',
            'summary' => 'Join us each Sunday at 11:00am, with breakfast or brunch together on the first Sunday of the month.',
            'linkLabel' => 'Plan your visit',
            'linkUrl' => '/visit-us',
        ],
        [
            'eyebrow' => 'Seasonal',
            'title' => 'Special services and community events',
            'summary' => 'Carol services, Good Friday, cinema nights, Acoustic Cafe, and seasonal gatherings happen through the year.',
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
            || !str_contains($currentItemsJson, 'Community Cafe')
            || str_contains($currentItemsJson, 'Latest sermons');
        $blockData = [
            'title' => in_array($currentTitle, $legacyTitles, true) ? $defaultTitle : ($currentTitle ?: $blockData['title']),
            'intro' => in_array($currentIntro, $legacyIntros, true) ? $defaultIntro : ($currentIntro ?: $blockData['intro']),
            'layout' => 'compact',
            'itemsJson' => $shouldRefreshItems ? $blockData['itemsJson'] : $currentItemsJson,
            'primaryButtonLabel' => trim((string) ($controller->primaryButtonLabel ?? $blockData['primaryButtonLabel'])),
            'primaryButtonUrl' => trim((string) ($controller->primaryButtonUrl ?? $blockData['primaryButtonUrl'])),
            'secondaryButtonLabel' => in_array($currentSecondaryLabel, $legacySecondaryLabels, true) ? $defaultSecondaryButtonLabel : $currentSecondaryLabel,
            'secondaryButtonUrl' => in_array($currentSecondaryUrl, $legacySecondaryUrls, true) ? $defaultSecondaryButtonUrl : $currentSecondaryUrl,
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
