<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$page = Page::getByPath('/visit-us', 'ACTIVE');
$legacyPage = null;

if (!$page || $page->isError()) {
    $legacyPage = Page::getByPath('/im-new', 'ACTIVE');
}

if ((!$page || $page->isError()) && (!$legacyPage || $legacyPage->isError())) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$root || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => 'Visit Us?',
            'cHandle' => 'visit-us',
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /visit-us.</info>');
} elseif ((!$page || $page->isError()) && $legacyPage && !$legacyPage->isError()) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
    $pageType = PageType::getByHandle('page');
    $fullTemplate = PageTemplate::getByHandle('full');

    if (!$root || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => 'Visit Us?',
            'cHandle' => 'visit-us',
            'cDescription' => (string) $legacyPage->getCollectionDescription(),
        ],
        $fullTemplate
    );

    $legacyPage->moveToTrash();
    $output->writeln('<info>Created /visit-us and moved /im-new to trash.</info>');
}

$page->update([
    'cName' => 'Visit Us?',
    'cHandle' => 'visit-us',
    'cDescription' => "Thinking about coming to Millbrook? You’d be very welcome. Here’s what happens on a Sunday, what to expect, and how we can help you feel at home.",
]);

$area = Area::getOrCreate($page, 'Main');
$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$blocks = [
    <<<'HTML'
<div class="content-intro">
  <p>We know visiting a church can feel like a big step, especially if you are not sure what to expect. Whether you are new to Larne, new to church, returning after a while, or just quietly curious, we would love to help you feel at home.</p>
</div>
HTML,
    <<<'HTML'
<div class="info-strip">
  <div class="info-strip__item">
    <span class="info-strip__label">Sunday Worship</span>
    <p class="info-strip__value">11:00am</p>
  </div>
  <div class="info-strip__item">
    <span class="info-strip__label">Location</span>
    <p class="info-strip__value">Millbrook Community Centre, Larne</p>
  </div>
  <div class="info-strip__item">
    <span class="info-strip__label">Before the service</span>
    <p>Most people arrive a few minutes early to settle in, say hello, and find a seat.</p>
    <p><a href="/contact">Contact Us</a></p>
  </div>
</div>
HTML,
    <<<'HTML'
<section class="content-section">
<h2>What Sundays Are Like</h2>
<p>Our Sunday service usually includes singing, prayer, Bible reading, and a short talk. Sometimes we share communion together. We are a welcoming, multi-generational church, and whether church feels familiar to you or completely new, you will be very welcome here.</p>
<p>You will not be asked to say anything out loud or do anything you are uncomfortable with. Tea and coffee usually follow the service, and we want Millbrook to feel like a place where people can come honestly, meet with God, and get to know others at their own pace.</p>
</section>
HTML,
    <<<'HTML'
<div class="card-grid">
  <div class="card-grid__item">
    <h3>Come as you are</h3>
    <p>There is no dress code. Some people dress more casually, some more smartly, but most importantly we want you to feel comfortable.</p>
  </div>
  <div class="card-grid__item">
    <h3>A welcoming atmosphere</h3>
    <p>We are a family-friendly church made up of people of different ages and backgrounds.</p>
  </div>
  <div class="card-grid__item">
    <h3>Worship and teaching</h3>
    <p>Our services include singing, prayer, and Bible teaching, with a desire to keep Jesus at the centre of all we do.</p>
  </div>
  <div class="card-grid__item">
    <h3>Questions welcome</h3>
    <p>You do not need to know exactly what you are doing. If you are exploring faith, you are very welcome here.</p>
  </div>
</div>
HTML,
    <<<'HTML'
<section class="content-section content-section--family">
<h2>Children &amp; Families</h2>
<p>Children and families are an important part of church life at Millbrook. We want children to feel welcome, safe, and included.</p>
<p>We offer creche and children’s ministry during our Sunday gatherings, and the people who serve in those areas are safely recruited and checked. We know church with children can be noisy, wriggly, and unpredictable, and that is okay.</p>
<ul class="highlight-list">
  <li>Children are welcome and included in church life</li>
  <li>Creche and children’s provision on Sundays</li>
  <li>Safely recruited and checked volunteers</li>
  <li>A family-friendly and multi-generational atmosphere</li>
</ul>
</section>
HTML,
    <<<'HTML'
<section class="content-section content-section--about">
<h2>A Bit About Us</h2>
<p>Millbrook is part of the Church of the Nazarene, a global Christian family shaped by grace, holiness, compassion, and serving local communities well. We are a Christ-centred church in Larne seeking to worship God, grow in faith, and love our neighbours.</p>
<p>We want to be a church where people of all ages can encounter Jesus, build genuine relationships, and find a place to belong.</p>
<p><a href="/about/who-we-are">Learn more about our church</a></p>
</section>
HTML,
    <<<'HTML'
<section class="faq-section">
<h2>Questions You Might Have</h2>
<div class="faq-accordion">
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">What time does the service start?</summary>
    <div class="faq-accordion__answer">
      <p>Our Sunday service starts at 11:00am, and most people arrive a few minutes beforehand.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">What should I wear?</summary>
    <div class="faq-accordion__answer">
      <p>Whatever helps you feel comfortable. There is no dress code.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">Can I come if I’m not sure what I believe?</summary>
    <div class="faq-accordion__answer">
      <p>Yes. You do not need to have everything figured out before visiting. You are very welcome to come, observe, ask questions, and take things at your own pace.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">Is there something for children?</summary>
    <div class="faq-accordion__answer">
      <p>Yes. Children and families are a valued part of church life, and we provide children’s provision during our Sunday gatherings.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">Do I need to bring anything?</summary>
    <div class="faq-accordion__answer">
      <p>No. Just come as you are. We will help you find your way around when you arrive.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">Will I have to give money?</summary>
    <div class="faq-accordion__answer">
      <p>No. Giving is part of worship for those who call Millbrook home, but visitors should feel no pressure to give.</p>
    </div>
  </details>
  <details class="faq-accordion__item">
    <summary class="faq-accordion__summary">Could someone meet me when I arrive?</summary>
    <div class="faq-accordion__answer">
      <p>Yes. If it would help, get in touch before Sunday and we will do our best to make your arrival easier.</p>
    </div>
  </details>
</div>
</section>
HTML,
    <<<'HTML'
<div class="action-panel">
  <h2>We’d Love to Welcome You</h2>
  <p>If you are thinking of visiting, we would love to see you on a Sunday. And if you would like to ask anything before you come, please get in touch.</p>
  <div class="action-panel__buttons">
    <a class="button button--primary" href="/contact">Contact Us</a>
    <a class="button button--secondary" href="mailto:info@millbrooknazarene.co.uk">Email the Church</a>
  </div>
</div>
HTML,
];

foreach ($blocks as $content) {
    $page->addBlock($contentBlockType, $area, ['content' => $content]);
}

$output->writeln('<info>Updated the Visit Us page with block-based content.</info>');

return 0;
