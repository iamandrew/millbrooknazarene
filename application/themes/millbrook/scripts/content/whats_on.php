<?php

return [
    'name' => "What's On",
    'description' => 'A simple guide to the regular rhythms, seasonal gatherings, and community events at Millbrook.',
    'content' => <<<'HTML'
<div class="content-guide content-guide--whats-on-grid">
  <section class="whats-on-intro">
    <div>
      <p class="content-kicker">What's On</p>
      <h2>A simple guide to what happens at Millbrook.</h2>
      <p class="content-guide__lede">Church life has a regular rhythm, but it is not always the same every week. This page gives you the shape of what usually happens, rather than a live events calendar.</p>
      <p>If you are new, Sunday morning is always a good place to begin. For one-off events, current dates, or booking details, check social media, the newsletter, or get in touch.</p>
    </div>
    <aside class="whats-on-intro__note">
      <strong>Looking for a date?</strong>
      <p>One-off events are normally advertised through church notices, Facebook, Instagram, and the weekly newsletter.</p>
    </aside>
  </section>

  <section class="whats-on-board" aria-labelledby="whats-on-weekly">
    <div class="whats-on-board__header">
      <div>
        <p class="content-kicker">Weekly</p>
        <h2 id="whats-on-weekly">Weekly rhythm.</h2>
      </div>
      <p>Some gatherings are open to everyone. Others are for a particular age or stage, but you are always welcome to ask where to start.</p>
    </div>

    <div class="whats-on-cards whats-on-cards--weekly">
      <a class="whats-on-card whats-on-card--blue" href="/visit-us">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Every Sunday</span>
          <h3>Sunday worship</h3>
          <p>We meet at 11:00am for worship, prayer, Bible teaching, and time together.</p>
          <span class="whats-on-card__meta">Everyone welcome | Plan your visit</span>
        </div>
      </a>

      <article class="whats-on-card whats-on-card--lime">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Sunday mornings</span>
          <h3>Sunday School</h3>
          <p>Children are included in church life, with Sunday School usually available during term time.</p>
          <span class="whats-on-card__meta">For children | During the service</span>
        </div>
      </article>

      <a class="whats-on-card whats-on-card--purple" href="/community/cheesy-nachos">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Sunday evenings</span>
          <h3>Youth</h3>
          <p>A relaxed space for secondary school age young people, with snacks, games, teaching, trips, and time together.</p>
          <span class="whats-on-card__meta">6:30-8:00pm | Secondary school age</span>
        </div>
      </a>

      <a class="whats-on-card whats-on-card--blue" href="/community/homegroups">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Midweek</span>
          <h3>Homegroup</h3>
          <p>An informal gathering to talk about faith, ask questions, pray, and share tea, coffee, and biscuits.</p>
          <span class="whats-on-card__meta">Wednesday evenings | Ask for the address</span>
        </div>
      </a>

      <article class="whats-on-card whats-on-card--coral">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Community</span>
          <h3>Cafe Fit</h3>
          <p>A weekly community space that supports wellbeing, connection, and friendship.</p>
          <span class="whats-on-card__meta">Open to the community</span>
        </div>
      </article>

      <article class="whats-on-card whats-on-card--dark">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Community</span>
          <h3>Community Cafe</h3>
          <p>A welcoming space to connect with others locally, enjoy conversation, and find support through the week.</p>
          <span class="whats-on-card__meta">Open to the community</span>
        </div>
      </article>
    </div>
  </section>

  <section class="whats-on-board" aria-labelledby="whats-on-monthly">
    <div class="whats-on-board__header">
      <div>
        <p class="content-kicker">Monthly</p>
        <h2 id="whats-on-monthly">Monthly rhythm.</h2>
      </div>
      <p>Monthly gatherings are a good way to get to know people at an easier pace.</p>
    </div>

    <div class="whats-on-cards whats-on-cards--monthly">
      <article class="whats-on-card whats-on-card--blue">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">First Sunday</span>
          <h3>First Breakfast</h3>
          <p>On the first Sunday of each month, we usually share breakfast or brunch, hear someone's story, or welcome a guest speaker.</p>
          <span class="whats-on-card__meta">Food, conversation, and church family</span>
        </div>
      </article>

      <a class="whats-on-card whats-on-card--dark" href="/community/mens-ministry">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Monthly</span>
          <h3>Men's Ministry</h3>
          <p>Coffee catch-ups, meals, games, activities, and space for men to connect and encourage one another.</p>
          <span class="whats-on-card__meta">Men aged 18+</span>
        </div>
      </a>

      <a class="whats-on-card whats-on-card--purple" href="/community/womens-ministry">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Monthly</span>
          <h3>Abide Women's Ministry</h3>
          <p>A relaxed gathering for women to pause, connect, enjoy time together, and encourage one another.</p>
          <span class="whats-on-card__meta">Women aged 18+</span>
        </div>
      </a>
    </div>
  </section>

  <section class="whats-on-board" aria-labelledby="whats-on-seasonal">
    <div class="whats-on-board__header">
      <div>
        <p class="content-kicker">Seasonal</p>
        <h2 id="whats-on-seasonal">Seasonal moments.</h2>
      </div>
      <p>These are advertised when dates are confirmed, so it is worth checking before you come.</p>
    </div>

    <div class="whats-on-cards whats-on-cards--seasonal">
      <article class="whats-on-card whats-on-card--coral">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Christian calendar</span>
          <h3>Special services</h3>
          <p>We mark significant moments together, including Christmas carols, Good Friday, and other seasonal services.</p>
          <span class="whats-on-card__meta">Open to everyone</span>
        </div>
      </article>

      <article class="whats-on-card whats-on-card--lime">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Summer</span>
          <h3>Kids Summer Club</h3>
          <p>A seasonal activity for primary school aged children, with registration details shared when booking opens.</p>
          <span class="whats-on-card__meta">Booking usually required</span>
        </div>
      </article>

      <article class="whats-on-card whats-on-card--purple">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Women</span>
          <h3>Soul Saturday</h3>
          <p>A seasonal gathering for women, with time to connect, be encouraged, and share life together.</p>
          <span class="whats-on-card__meta">For women</span>
        </div>
      </article>

      <article class="whats-on-card whats-on-card--blue">
        <div class="whats-on-card__body">
          <span class="whats-on-card__eyebrow">Community</span>
          <h3>Community events</h3>
          <p>Cinema nights, Acoustic Cafe, celebrations, and other local gatherings happen at different points in the year.</p>
          <span class="whats-on-card__meta">Dates advertised as they come up</span>
        </div>
      </article>
    </div>
  </section>

  <section class="whats-on-help">
    <div>
      <p class="content-kicker">New or unsure?</p>
      <h2>We can help you find the right place to start.</h2>
      <p>It is natural to feel unsure about walking into something new. If you message us before coming, we can let you know what to expect and help you find a friendly face.</p>
    </div>
    <div class="whats-on-help__links">
      <p><strong>General enquiries</strong><br><a href="mailto:info@millbrooknazarene.co.uk">info@millbrooknazarene.co.uk</a></p>
      <p><strong>Coming on Sunday?</strong><br><a href="/visit-us">Plan your first visit</a></p>
      <p><strong>Want the wider picture?</strong><br><a href="/community">Explore Church Life</a></p>
    </div>
  </section>
</div>
HTML,
];
