<?php

return [
    'name' => 'Giving',
    'description' => 'Ways to give and support the work of Millbrook Church.',
    'content' => <<<'HTML'
<div class="content-guide content-guide--giving">
  <section class="giving-intro giving-intro--simple">
    <div class="giving-intro__copy">
      <p class="content-kicker">Giving</p>
      <h2>Ways to give and support the work of the church.</h2>
      <p class="content-guide__lede">Millbrook Church of the Nazarene is financed through regular giving.</p>
      <p>Giving to the church is our primary way of giving back to God for bringing faith, hope, and the love of his son Jesus to the world, as well as supporting our community.</p>
      <p>There are many ways to give and support the work of the church. Details are below.</p>
    </div>
  </section>

  <section class="giving-details">
    <div class="giving-details__copy">
      <section class="content-guide-card">
        <p class="content-kicker">Bank transfer</p>
        <h3>Give by bank transfer.</h3>
        <p>These details can be used for a one-off bank transfer. If you would like to make a regular contribution, you can set up a standing order through online banking or by visiting your local bank branch.</p>
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
            <dd>Firstname/Surname</dd>
          </div>
        </dl>
      </section>

      <section class="content-guide-card content-guide-card--lime">
        <p class="content-kicker">Gift Aid</p>
        <h3>Gift Aid can add 25p for every £1 you give.</h3>
        <p>If you are a UK taxpayer, you can boost your donation through Gift Aid.</p>
        <p>To allow us to claim Gift Aid on your behalf, please complete a Gift Aid declaration <a href="/download_file/view/85/493">here</a> and send it to <a href="mailto:finance@millbrooknazarene.co.uk?subject=Gift%20Aid%20Declaration">finance@millbrooknazarene.co.uk</a>.</p>
        <p>If you are already registered for Gift Aid, you can give online and quote your unique number in the memo field. If you are unsure of your number, contact <a href="mailto:finance@millbrooknazarene.co.uk?subject=I%20forgot%20my%20unique%20gift%20aid%20number">finance@millbrooknazarene.co.uk</a>.</p>
      </section>
    </div>

    <section class="giving-panel" data-giving-widget data-campaign-id="73RaDUKovkVWzLrxJioCJi" data-api-base="https://api.test-givealittle.cyb.dev" data-website-base="https://test-givealittle.cyb.dev" data-checkout-prefix="/c/" data-return-path="/giving?thanks=1" data-tag="millbrook-web-test">
      <div class="giving-panel__header">
        <div>
          <p class="content-kicker">Online giving</p>
          <h2>Give online.</h2>
          <p>You can give a one-off donation securely online using the form below.</p>
          <p data-giving-campaign-name>Millbrook Church of the Nazarene</p>
        </div>
        <p class="giving-panel__mode">Test mode</p>
      </div>

      <div class="giving-thanks" data-giving-thanks hidden>
        <strong>Thank you.</strong>
        <p>If you have just completed a gift, thank you for supporting the work of Millbrook Church.</p>
      </div>

      <form class="giving-form" data-giving-form>
        <fieldset class="giving-form__section">
          <legend>Choose an amount</legend>
          <div class="giving-form__amounts" data-giving-amounts>
            <button class="giving-amount-option" type="button" data-amount="5" aria-pressed="false">£5</button>
            <button class="giving-amount-option" type="button" data-amount="10" aria-pressed="false">£10</button>
            <button class="giving-amount-option" type="button" data-amount="15" aria-pressed="false">£15</button>
            <button class="giving-amount-option" type="button" data-amount="20" aria-pressed="false">£20</button>
            <button class="giving-amount-option" type="button" data-amount="30" aria-pressed="false">£30</button>
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
          <p class="giving-status" data-giving-status role="status" aria-live="polite">Loading the Give A Little test campaign…</p>
        </div>
      </form>

      <div class="giving-panel__footer">
        <p>This is currently connected to Give A Little’s test system while the new site is in development.</p>
      </div>
    </section>
  </section>
</div>
HTML,
];
