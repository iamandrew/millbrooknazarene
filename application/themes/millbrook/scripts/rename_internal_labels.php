<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$replaceMainArea = static function (Page $page, string $html) use ($contentBlockType, $output): void {
    $area = Area::getOrCreate($page, 'Main');
    foreach ($area->getAreaBlocksArray($page) as $block) {
        $block->deleteBlock();
    }

    $page->addBlock($contentBlockType, $area, ['content' => $html]);
    $output->writeln(sprintf('Updated content: %s', $page->getCollectionPath()));
};

$pageMap = [
    '/community/cheesy-nachos' => [
        'name' => 'Youth',
        'description' => 'A friendly social space for young people to connect, laugh, and explore faith.',
        'content' => <<<'HTML'
<section class="content-section">
<h2>Youth</h2>
<p>Youth is a relaxed space for young people to meet, have fun, and build friendships in a safe and welcoming environment. It is a place where faith conversations can happen naturally alongside games, food, and shared experiences.</p>

<h3>A Typical Gathering</h3>
<ul>
  <li>Games, conversation, and time to hang out</li>
  <li>Short faith-based input or discussion</li>
  <li>A friendly atmosphere where new people can settle in quickly</li>
  <li>Leaders who want to support and encourage young people well</li>
</ul>
</section>
HTML,
    ],
    '/community/mens-ministry' => [
        'name' => 'Men',
        'description' => 'A space for men to grow in faith, friendship, and service together.',
        'content' => <<<'HTML'
<section class="content-section">
<h2>Men</h2>
<p>Gatherings for men create opportunities for connection, encouragement, and spiritual growth. Through conversation, prayer, and shared activities, we want men to be strengthened in faith and equipped for everyday discipleship.</p>

<h3>Why It Matters</h3>
<p>It can be difficult to build meaningful friendships and make time for spiritual growth in the middle of work, family life, and responsibility. This group creates room for honest conversation, prayer, and mutual encouragement.</p>
</section>
HTML,
    ],
    '/community/womens-ministry' => [
        'name' => 'Women',
        'description' => 'Gatherings that encourage friendship, prayer, learning, and spiritual growth.',
        'content' => <<<'HTML'
<section class="content-section">
<h2>Women</h2>
<p>Gatherings for women offer spaces to support one another and keep growing in faith. Some are social, some are reflective, and some focus more directly on Bible study and prayer, but each one is intended to strengthen relationships and discipleship.</p>

<h3>What It Looks Like</h3>
<ul>
  <li>Regular opportunities to meet and build friendship</li>
  <li>Times of prayer, conversation, and Bible reflection</li>
  <li>Encouragement for women in different ages and stages of life</li>
  <li>Space to share experiences and walk alongside one another well</li>
</ul>
</section>
HTML,
    ],
    '/community/creche' => [
        'name' => 'Creche',
        'description' => 'Support for families with very young children during Sunday gatherings.',
        'content' => <<<'HTML'
<section class="content-section">
<h2>Creche</h2>
<p>This part of church life exists to support families with very young children and help Sundays feel accessible for everyone. It provides a safe and caring environment where little ones can settle and parents can feel more able to participate in the service.</p>

<h3>For Families</h3>
<p>If you are visiting with a baby or toddler, please feel free to speak to someone when you arrive and we will help you find your way. We want parents and carers to feel supported rather than pressured.</p>
</section>
HTML,
    ],
];

foreach ($pageMap as $path => $data) {
    $page = Page::getByPath($path, 'ACTIVE');
    if (!$page instanceof Page || $page->isError()) {
        $output->writeln(sprintf('<comment>Skipped missing page: %s</comment>', $path));
        continue;
    }

    $page->update([
        'cName' => $data['name'],
        'cDescription' => $data['description'],
    ]);

    $replaceMainArea($page, $data['content']);
}

$output->writeln('<info>Renamed internal labels to public-facing page names.</info>');

return 0;
