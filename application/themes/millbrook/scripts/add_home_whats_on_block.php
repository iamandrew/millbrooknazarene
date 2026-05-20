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

$defaultTitle = 'Ways to connect this month.';
$legacyTitles = [
    'A few simple ways to connect through the month.',
];
$defaultIntro = 'Regular rhythms across the week help people pray, connect, and grow together.';
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
            'eyebrow' => 'Sunday',
            'title' => 'Worship at 11:00am',
            'summary' => 'Join us each Sunday for worship, prayer, Bible teaching, and time together afterwards.',
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
            'eyebrow' => 'Latest sermons',
            'title' => 'Catch up on recent teaching',
            'summary' => 'Listen back to recent sermons and Bible teaching from Millbrook.',
            'linkLabel' => 'Latest sermons',
            'linkUrl' => '/resources/sermons',
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
        $blockData = [
            'title' => in_array($currentTitle, $legacyTitles, true) ? $defaultTitle : ($currentTitle ?: $blockData['title']),
            'intro' => in_array($currentIntro, $legacyIntros, true) ? $defaultIntro : ($currentIntro ?: $blockData['intro']),
            'layout' => 'compact',
            'itemsJson' => trim((string) ($controller->itemsJson ?? $blockData['itemsJson'])) ?: $blockData['itemsJson'],
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
