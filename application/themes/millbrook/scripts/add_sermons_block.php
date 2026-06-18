<?php

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/resources/sermons', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $output->writeln('<error>Could not find /resources/sermons.</error>');
    return 1;
}

$desiredDescription = 'Catch up on Sunday sermons and Bible teaching from Millbrook. Whether you missed a gathering or want to revisit a message during the week, you can listen here or continue on Spotify or Apple Podcasts.';
$legacyDescriptions = [
    '',
    'Recent teaching and sermon series from Millbrook Church.',
    'Listen back to recent sermons and Bible teaching from Millbrook.',
    'Catch up on Sunday sermons and Bible teaching from Millbrook. Whether you missed a gathering or want to revisit a message during the week, you can listen here and find more on Spotify.',
    'Catch up on Sunday sermons and Bible teaching from Millbrook. Whether you missed a gathering or want to revisit a message during the week, you can listen here or open the full podcast on Spotify or Apple Podcasts.',
];

if (in_array(trim((string) $page->getCollectionDescription()), $legacyDescriptions, true)) {
    $page->update([
        'cDescription' => $desiredDescription,
    ]);
    $page = Page::getByID($page->getCollectionID(), 'ACTIVE');
}

$disableHeroImageKey = CollectionKey::getByHandle('disable_hero_image');
if ($disableHeroImageKey) {
    $page->setAttribute($disableHeroImageKey, false);
    $page = Page::getByID($page->getCollectionID(), 'ACTIVE');
}

$blockType = BlockType::getByHandle('latest_sermons');
if (!$blockType) {
    $blockType = BlockType::installBlockType('latest_sermons');
    $output->writeln('<info>Installed block type: latest_sermons</info>');
}

if (!$blockType) {
    $output->writeln('<error>Could not install or load the Latest Sermons block type.</error>');
    return 1;
}

$blockType->refresh();

$area = Area::getOrCreate($page, 'Main');
$existingBlocks = $area->getAreaBlocksArray($page);
$defaultSpotifyFeedUrl = 'https://anchor.fm/s/113054664/podcast/rss';
$defaultSpotifyShowUrl = 'https://open.spotify.com/show/033njtKzXFC2vPB33mR1UV';
$defaultApplePodcastUrl = 'https://podcasts.apple.com/gb/podcast/millbrook-church-of-the-nazarene/id1896866584';

$sermonsBlockData = [
    'title' => '',
    'intro' => '',
    'sourceType' => 'spotify',
    'spotifyFeedUrl' => $defaultSpotifyFeedUrl,
    'displayLimit' => 8,
    'showDescriptions' => 1,
    'showPlayer' => 1,
    'showArchiveButton' => 1,
    'archiveButtonLabel' => 'Listen on Spotify',
    'archiveButtonUrl' => $defaultSpotifyShowUrl,
    'applePodcastButtonLabel' => 'Apple Podcasts',
    'applePodcastButtonUrl' => $defaultApplePodcastUrl,
];

foreach ($existingBlocks as $block) {
    if ($block->getBlockTypeHandle() !== 'latest_sermons') {
        continue;
    }

    $controller = $block->getController();
    $sourceType = in_array((string) ($controller->sourceType ?? ''), ['concrete_uploads', 'spotify'], true) ? (string) $controller->sourceType : 'spotify';
    $spotifyFeedUrl = trim((string) ($controller->spotifyFeedUrl ?? '')) ?: $defaultSpotifyFeedUrl;
    if ($sourceType === 'concrete_uploads' && $spotifyFeedUrl !== '') {
        $sourceType = 'spotify';
    }
    $archiveButtonLabel = trim((string) ($controller->archiveButtonLabel ?? ''));
    if ($archiveButtonLabel === '' || $archiveButtonLabel === 'Latest Sermons') {
        $archiveButtonLabel = 'Listen on Spotify';
    }
    $archiveButtonUrl = trim((string) ($controller->archiveButtonUrl ?? ''));
    if ($archiveButtonUrl === '' || $archiveButtonUrl === '/resources/sermons') {
        $archiveButtonUrl = $defaultSpotifyShowUrl;
    }
    $applePodcastButtonLabel = trim((string) ($controller->applePodcastButtonLabel ?? ''));
    if ($applePodcastButtonLabel === '') {
        $applePodcastButtonLabel = 'Apple Podcasts';
    }
    $applePodcastButtonUrl = trim((string) ($controller->applePodcastButtonUrl ?? ''));
    if ($applePodcastButtonUrl === '') {
        $applePodcastButtonUrl = $defaultApplePodcastUrl;
    }

    $sermonsBlockData = [
        'title' => trim((string) ($controller->title ?? '')),
        'intro' => trim((string) ($controller->intro ?? '')),
        'sourceType' => $sourceType,
        'spotifyFeedUrl' => $spotifyFeedUrl,
        'displayLimit' => max(1, min((int) ($controller->displayLimit ?? 8), 8)),
        'showDescriptions' => isset($controller->showDescriptions) ? (!empty($controller->showDescriptions) ? 1 : 0) : 1,
        'showPlayer' => !empty($controller->showPlayer) ? 1 : 0,
        'showArchiveButton' => 1,
        'archiveButtonLabel' => $archiveButtonLabel,
        'archiveButtonUrl' => $archiveButtonUrl,
        'applePodcastButtonLabel' => $applePodcastButtonLabel,
        'applePodcastButtonUrl' => $applePodcastButtonUrl,
    ];
    break;
}

foreach ($existingBlocks as $block) {
    $block->deleteBlock();
}

$page->addBlock($blockType, $area, $sermonsBlockData);

$output->writeln('<info>Rebuilt /resources/sermons with a cleaner latest sermons block.</info>');

return 0;
