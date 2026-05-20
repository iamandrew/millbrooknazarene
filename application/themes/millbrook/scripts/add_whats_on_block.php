<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/community/whats-on', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /community/whats-on.</error>');
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

$area = Area::getOrCreate($page, 'Main');
$existingBlocks = $area->getAreaBlocksArray($page);

$blockData = [
    'title' => 'A few simple ways to connect through the month.',
    'intro' => 'Alongside Sunday worship, there are regular gatherings, groups, and church rhythms that help people pray, connect, and grow together.',
    'layout' => 'cards',
    'itemsJson' => json_encode([
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
            'eyebrow' => 'Looking ahead',
            'title' => 'Seasonal events and church-wide gatherings',
            'summary' => 'Special services, shared meals, and occasional events help mark the year together as a church family.',
            'linkLabel' => 'Get in touch',
            'linkUrl' => '/contact',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'primaryButtonLabel' => 'Visit Us?',
    'primaryButtonUrl' => '/visit-us',
    'secondaryButtonLabel' => 'Latest Sermons',
    'secondaryButtonUrl' => '/resources/sermons',
];

foreach ($existingBlocks as $block) {
    if ($block->getBlockTypeHandle() === 'whats_on_block') {
        $controller = $block->getController();
        $blockData = [
            'title' => trim((string) ($controller->title ?? $blockData['title'])),
            'intro' => trim((string) ($controller->intro ?? $blockData['intro'])),
            'layout' => in_array((string) ($controller->layout ?? ''), ['cards', 'compact'], true) ? (string) $controller->layout : $blockData['layout'],
            'itemsJson' => trim((string) ($controller->itemsJson ?? $blockData['itemsJson'])) ?: $blockData['itemsJson'],
            'primaryButtonLabel' => trim((string) ($controller->primaryButtonLabel ?? $blockData['primaryButtonLabel'])),
            'primaryButtonUrl' => trim((string) ($controller->primaryButtonUrl ?? $blockData['primaryButtonUrl'])),
            'secondaryButtonLabel' => trim((string) ($controller->secondaryButtonLabel ?? $blockData['secondaryButtonLabel'])),
            'secondaryButtonUrl' => trim((string) ($controller->secondaryButtonUrl ?? $blockData['secondaryButtonUrl'])),
        ];
        break;
    }
}

foreach ($existingBlocks as $block) {
    $block->deleteBlock();
}

$page->addBlock($blockType, $area, $blockData);

$output->writeln('<info>Rebuilt /community/whats-on with the structured What’s On block.</info>');

return 0;
