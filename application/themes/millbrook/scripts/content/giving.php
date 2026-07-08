<?php

return [
    'name' => 'Giving',
    'description' => 'Ways to give and support the work of Millbrook Church.',
    'content' => <<<'HTML'
<div class="content-guide content-guide--giving">
  <section class="giving-intro giving-intro--simple">
    <div class="giving-intro__copy">
      <p class="content-kicker">Giving</p>
      <h2>Supporting the life and work of Millbrook.</h2>
      <p class="content-guide__lede">Giving helps support and grow the ministries of the church as we serve God's kingdom and our local community.</p>
      <p>We are grateful for every gift, whether it supports weekly worship, practical care in the community, or specific areas of ministry.</p>
      <p>If you are visiting or new to Millbrook, please do not feel under any pressure to give. Giving should never be a barrier to coming along.</p>
    </div>
  </section>

  <section class="giving-panel giving-panel--featured" data-giving-widget data-campaign-id="millbrook-nazarene-giving" data-website-base="https://givealittle.co" data-donation-prefix="/c/" data-donation-action="/givealittle/start" data-public-campaign-url="https://givealittle.co/c/millbrook-nazarene-giving" data-return-path="/giving?thanks=1" data-tag="millbrook-web" data-fetch-campaign="false" data-donation-limit="1000">
    <div class="giving-panel__header">
      <div>
        <p class="content-kicker">Online giving</p>
        <h2>Give online through Give a Little.</h2>
        <p>You can give securely online using the form below. You can choose a one-off gift or, where available, a monthly gift.</p>
        <p data-giving-campaign-name>Millbrook Church of the Nazarene</p>
      </div>
    </div>

    <div class="giving-thanks" data-giving-thanks hidden>
      <strong>Thank you.</strong>
      <p>If you have just completed a gift, thank you for supporting the work of Millbrook Church.</p>
    </div>

    <form class="giving-form" data-giving-form>
      <fieldset class="giving-form__section">
        <legend>Choose an amount</legend>
        <div class="giving-form__amounts" data-giving-amounts>
          <button class="giving-amount-option" type="button" data-amount="10" aria-pressed="false">£10</button>
          <button class="giving-amount-option" type="button" data-amount="25" aria-pressed="false">£25</button>
          <button class="giving-amount-option" type="button" data-amount="50" aria-pressed="false">£50</button>
        </div>
        <label class="giving-custom-amount">
          <span>Or enter another amount</span>
          <span class="giving-custom-amount__field">
            <span>£</span>
            <input type="number" inputmode="decimal" min="1" step="0.01" data-giving-custom-amount aria-label="Custom giving amount">
          </span>
        </label>
      </fieldset>

      <fieldset class="giving-form__section">
        <legend>Choose frequency</legend>
        <div class="giving-frequency" data-giving-frequency>
          <label>
            <input type="radio" name="giving_frequency" value="one_off" checked>
            <span>One-off gift</span>
          </label>
          <label>
            <input type="radio" name="giving_frequency" value="monthly">
            <span>Monthly gift</span>
          </label>
        </div>
      </fieldset>

      <div class="giving-form__actions">
        <button class="btn giving-submit" type="submit" data-giving-submit>Continue securely</button>
        <div class="giving-form__feedback">
          <p class="giving-status" data-giving-status role="status" aria-live="polite">Loading the Give A Little campaign…</p>
          <a class="giving-fallback-link" href="https://givealittle.co/c/millbrook-nazarene-giving" target="_blank" rel="noopener" data-giving-fallback-link hidden>Open the Give A Little donation page in a new tab</a>
        </div>
      </div>
    </form>

    <div class="giving-panel__footer">
      <p><strong>No pressure.</strong> Online giving is here for those who want to use it. You are welcome at Millbrook whether or not you give financially.</p>
      <p>Give A Little handles online gifts securely and will guide you through the next steps.</p>
    </div>
  </section>

  <section class="giving-details giving-details--cards">
    <div class="giving-details__copy">
      <section class="content-guide-card content-guide-card--coral giving-method-card giving-method-card--intro">
        <p class="content-kicker">Other ways to give</p>
        <h3>Choose the way that works best for you.</h3>
        <p>You can also give by standing order, one-off bank transfer, or in person by cash, card or contactless where available.</p>
        <p>If you would like your gift to go towards a specific fund, please include the fund name as your reference where your giving method allows it, or contact the treasurer for help.</p>
      </section>

      <section class="content-guide-card giving-method-card giving-method-card--bank">
        <p class="content-kicker">Bank transfer</p>
        <h3>Give by bank transfer or standing order.</h3>
        <p>These details can be used for a one-off bank transfer. If you would like to give regularly, you can set up a standing order through online banking or by speaking to your bank.</p>
        <dl class="giving-bank-details">
          <div>
            <dt>Bank</dt>
            <dd>Ulster Bank</dd>
          </div>
          <div>
            <dt>Sort code</dt>
            <dd>98-04-00</dd>
          </div>
          <div>
            <dt>Account number</dt>
            <dd>24389099</dd>
          </div>
          <div>
            <dt>Account name</dt>
            <dd>Millbrook Church of the Nazarene</dd>
          </div>
          <div>
            <dt>Reference</dt>
            <dd>Firstname/Surname, or fund name if needed</dd>
          </div>
        </dl>
      </section>

      <section class="content-guide-card content-guide-card--lime giving-method-card giving-method-card--gift-aid">
        <p class="content-kicker">Gift Aid</p>
        <h3>Gift Aid can add 25p for every £1 you give.</h3>
        <ul class="giving-list">
          <li>If you are a UK taxpayer, Gift Aid allows the church to claim an extra 25p for every £1 you give, at no extra cost to you.</li>
          <li>To register, complete a Gift Aid declaration <a href="/download_file/view/85/493">here</a> and send it to <a href="mailto:finance@millbrooknazarene.co.uk?subject=Gift%20Aid%20Declaration">finance@millbrooknazarene.co.uk</a>. A paper form can also be requested from the treasurer.</li>
          <li>Once registered, please include your Gift Aid number on bank transfers, online gifts where possible, and cash giving envelopes.</li>
          <li>If you are a higher rate taxpayer and need a giving statement for your self assessment tax return, please contact the treasurer.</li>
        </ul>
      </section>
    </div>
  </section>

  <section class="giving-principles">
    <div class="giving-principles__intro">
      <p class="content-kicker">How gifts are used</p>
      <h2>Giving supports worship, care, community, and ministry.</h2>
      <p>Your giving helps make church life possible, from weekly worship and ministry costs to practical support for people in our church family and wider community.</p>
    </div>

    <div class="giving-funds">
      <article class="giving-fund-card">
        <p class="content-kicker">Millbrook Nazarene</p>
        <h3>Church operations and ministries</h3>
        <p>Supports the regular running of the church, Sunday worship, ministry activities, resources, and practical costs.</p>
      </article>

      <article class="giving-fund-card giving-fund-card--lime">
        <p class="content-kicker">Community Care Fund</p>
        <h3>Care for people in need</h3>
        <p>Helps support those in need within our church family and wider community in practical ways.</p>
      </article>

      <article class="giving-fund-card giving-fund-card--purple">
        <p class="content-kicker">Student Support Fund</p>
        <h3>Supporting pastoral formation</h3>
        <p>Helps support our work with the Nazarene pastoral mentorship programme.</p>
      </article>
    </div>
  </section>

  <section class="giving-privacy">
    <div>
      <p class="content-kicker">Privacy and questions</p>
      <h3>Your giving information is handled carefully.</h3>
      <p>We do not hold anyone's bank details for giving. The giving information we hold is limited to what is needed for Gift Aid and basic financial records, and is handled in line with data protection responsibilities.</p>
    </div>
    <div class="giving-privacy__actions">
      <a href="mailto:finance@millbrooknazarene.co.uk">Email finance</a>
      <a href="/contact">Contact the church</a>
    </div>
  </section>
</div>
HTML,
];
