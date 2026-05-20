<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');
$pageType = PageType::getByHandle('page');
$fullTemplate = PageTemplate::getByHandle('full');

if (!$contentBlockType || !$pageType || !$fullTemplate) {
    $output->writeln('<error>Missing required page type, template, or content block type.</error>');
    return 1;
}

$site = \Core::make('site')->getSite();
$root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
if (!$root || $root->isError()) {
    $output->writeln('<error>Unable to find the site home page.</error>');
    return 1;
}

$resolvePage = static function (array $paths) use ($root, $pageType, $fullTemplate, $output): ?Page {
    foreach ($paths as $index => $path) {
        $page = Page::getByPath($path, 'ACTIVE');
        if ($page instanceof Page && !$page->isError()) {
            return $page;
        }

        if ($index === 0) {
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            if (count($segments) === 1) {
                $page = $root->add(
                    $pageType,
                    [
                        'cName' => ucwords(str_replace('-', ' ', $segments[0])),
                        'cHandle' => $segments[0],
                    ],
                    $fullTemplate
                );
                $output->writeln(sprintf('Created page: %s', $page->getCollectionPath()));
                return $page;
            }
        }
    }

    return null;
};

$replaceMainArea = static function (Page $page, string $html) use ($contentBlockType, $output): void {
    $area = Area::getOrCreate($page, 'Main');
    foreach ($area->getAreaBlocksArray($page) as $block) {
        $block->deleteBlock();
    }

    $page->addBlock($contentBlockType, $area, ['content' => $html]);
    $output->writeln(sprintf('Updated content: %s', $page->getCollectionPath()));
};

$aboutPage = $resolvePage(['/about']);
$communityPage = $resolvePage(['/community', '/ministries']);
if (!$aboutPage || !$communityPage) {
    $output->writeln('<error>One or more key pages could not be resolved.</error>');
    return 1;
}

$sundaysPage = Page::getByPath('/sundays', 'ACTIVE');
if ($sundaysPage instanceof Page && !$sundaysPage->isError()) {
    $sundaysPage->moveToTrash();
    $output->writeln('Moved /sundays to trash after merging its content into /visit-us.');
}

$legacyCommunityPage = Page::getByPath('/ministries', 'ACTIVE');
if (
    $legacyCommunityPage instanceof Page
    && !$legacyCommunityPage->isError()
    && $legacyCommunityPage->getCollectionID() !== $communityPage->getCollectionID()
) {
    foreach ($legacyCommunityPage->getCollectionChildren('ACTIVE') as $childPage) {
        if (!$childPage instanceof Page || $childPage->isError()) {
            continue;
        }

        $existingChild = Page::getByPath('/community/' . $childPage->getCollectionHandle(), 'ACTIVE');
        if (!$existingChild || $existingChild->isError()) {
            $childPage->move($communityPage);
            $output->writeln(sprintf('Moved page: %s under /community', $childPage->getCollectionName()));
        }
    }

    $legacyCommunityPage->moveToTrash();
    $output->writeln('Moved /ministries to trash after creating /community.');
}

$aboutPage->update([
    'cName' => 'About',
    'cDescription' => 'Who Millbrook is, what we believe, and how we seek to follow Jesus together.',
]);

$communityPage->update([
    'cName' => 'Church Life',
    'cHandle' => 'community',
    'cDescription' => 'See how church life extends beyond Sunday through children, groups, prayer, and care.',
]);

$replaceMainArea($aboutPage, <<<'HTML'
<section class="content-section">
<h2>About Millbrook</h2>
<p>Millbrook is a local Church of the Nazarene congregation in Larne. We are a Christ-centred church seeking to worship God, grow in faith, and serve our local community with compassion, integrity, and hope.</p>
<p>We want to be a church where people of all ages can encounter Jesus, build genuine relationships, and find a place to belong.</p>
</section>

<section class="content-section">
<h2>Explore More</h2>
<div class="card-grid">
  <div class="card-grid__item">
    <h3>Who We Are</h3>
    <p>Learn more about the heart, story, and local identity of Millbrook.</p>
    <p><a href="/about/who-we-are">Read more</a></p>
  </div>
  <div class="card-grid__item">
    <h3>What We Believe</h3>
    <p>See the Christian convictions that shape our worship, prayer, and church life.</p>
    <p><a href="/about/what-we-believe">Read more</a></p>
  </div>
</div>
</section>
HTML);

$replaceMainArea($communityPage, <<<'HTML'
<section class="content-section">
<h2>In the Heart of the Community</h2>
<p>Church life at Millbrook is not only about what happens on a Sunday morning. We want to be a faithful, loving presence in Larne through worship, prayer, friendship, support, and shared life.</p>
<p>There are different ways for people of different ages and stages to belong, build relationships, and keep growing in faith together.</p>
</section>

<section class="content-section">
<h2>Explore Community Life</h2>
<div class="card-grid">
  <div class="card-grid__item">
    <h3>What’s On</h3>
    <p>Regular gatherings, seasonal events, and church rhythms through the month.</p>
    <p><a href="/community/whats-on">See what’s on</a></p>
  </div>
  <div class="card-grid__item">
    <h3>Homegroups</h3>
    <p>Smaller spaces for friendship, prayer, and opening the Bible together.</p>
    <p><a href="/community/homegroups">Explore homegroups</a></p>
  </div>
  <div class="card-grid__item">
    <h3>Children &amp; Families</h3>
    <p>How we welcome children and support families in the life of the church.</p>
    <p><a href="/community/children">Learn more</a></p>
  </div>
  <div class="card-grid__item">
    <h3>Men &amp; Women</h3>
    <p>Gatherings that help people encourage one another and keep growing in faith.</p>
    <p><a href="/community/mens-ministry">Men</a> / <a href="/community/womens-ministry">Women</a></p>
  </div>
</div>
</section>
HTML);

$output->writeln('<info>Updated visitor blueprint landing pages.</info>');

return 0;
