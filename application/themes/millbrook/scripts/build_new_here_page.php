<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;

$page = Page::getByPath('/im-new', 'ACTIVE');
if (!$page || $page->isError()) {
    $output->writeln('<error>Could not find /im-new.</error>');
    return 1;
}

$page->update([
    'cName' => 'New Here',
    'cDescription' => "Thinking of visiting Millbrook? We'd love to welcome you. Here's a little more about who we are, what a Sunday looks like, and what you can expect when you visit.",
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
  <p>We know visiting a church for the first time can feel a little uncertain, so we hope this page helps you feel more prepared and at ease.</p>
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
    <span class="info-strip__label">Need to ask something before you come?</span>
    <p>Get in touch and we’ll be happy to help.</p>
    <p><a href="/contact">Contact Us</a></p>
  </div>
</div>
HTML,
    <<<'HTML'
<section class="content-section">
<h2>What Sundays Are Like</h2>
<p>Our Sunday service includes worship, prayer, Bible teaching, and time together as a church family. We are a welcoming, multi-generational church, and whether you’ve been part of church for years or are simply exploring, you will be very welcome here.</p>
<p>You do not need to know all the words, understand everything, or dress a certain way before coming. We want Millbrook to feel like a place where people can come honestly, meet with God, and get to know others.</p>
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
    <p>We are a contemporary, family-friendly church made up of people of different ages and backgrounds.</p>
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
<p>We offer creche and children’s ministry during our Sunday gatherings, and the people who serve in those areas are safely recruited and checked.</p>
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
<p>Millbrook is a Church of the Nazarene congregation in Larne. We are a Christ-centred church seeking to worship God, grow in faith, and serve our local community.</p>
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
      <p>Our Sunday service starts at 11:00am.</p>
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
      <p>No. Just come as you are.</p>
    </div>
  </details>
</div>
</section>
HTML,
    <<<'HTML'
<div class="action-panel">
  <h2>We’d Love to Welcome You</h2>
  <p>If you’re thinking of visiting, we’d love to see you on a Sunday. And if you’d like to ask anything before you come, please get in touch.</p>
  <div class="action-panel__buttons">
    <a class="button button--primary" href="/contact">Contact Us</a>
    <a class="button button--secondary" href="/contact">Find Us</a>
  </div>
</div>
HTML,
];

foreach ($blocks as $content) {
    $page->addBlock($contentBlockType, $area, ['content' => $content]);
}

$output->writeln('<info>Updated the New Here page with block-based content.</info>');
