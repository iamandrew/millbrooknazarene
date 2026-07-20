<?php

return [
    'name' => 'Contact',
    'description' => 'Get in touch with Millbrook Church for general questions, visit enquiries, safeguarding, directions, and support.',
    'pre_form' => <<<'HTML'
<div class="contact-page">
  <section class="contact-intro">
    <div class="contact-intro__copy">
      <p class="content-kicker">Contact</p>
      <h2>How can we help?</h2>
      <p class="contact-intro__lede">Whether you have a question, want to plan a visit, need directions, or would like to speak to someone, you are welcome to get in touch.</p>
      <p>The form below is the best place for general enquiries. Your message will go to the church email inbox and can be passed to the right person if needed.</p>
    </div>
    <aside class="contact-intro__note">
      <strong>We aim to reply within two business days.</strong>
      <p>If your message is urgent or someone is at immediate risk, please contact the appropriate emergency or safeguarding service first.</p>
    </aside>
  </section>

  <section class="contact-info-grid" aria-label="Contact details">
    <article class="contact-info-card">
      <p class="content-kicker">Email</p>
      <h3>General enquiries</h3>
      <p><a href="mailto:info@millbrooknazarene.church">info@millbrooknazarene.church</a></p>
    </article>

    <article class="contact-info-card contact-info-card--lime">
      <p class="content-kicker">Visit</p>
      <h3>Millbrook Community Centre</h3>
      <p>Drumahoe Road, Millbrook, Larne, BT40 2PF.</p>
    </article>

    <article class="contact-info-card contact-info-card--purple">
      <p class="content-kicker">Safeguarding</p>
      <h3>Safeguarding enquiries</h3>
      <p><a href="mailto:safeguarding@millbrooknazarene.church">safeguarding@millbrooknazarene.church</a></p>
    </article>

    <article class="contact-info-card contact-info-card--coral">
      <p class="content-kicker">Pastoral support</p>
      <h3>Prayer, care, or support</h3>
      <p><a href="mailto:victoria@millbrooknazarene.church">victoria@millbrooknazarene.church</a></p>
    </article>

    <article class="contact-info-card">
      <p class="content-kicker">Social</p>
      <h3>Facebook and Instagram</h3>
      <p>Follow Millbrook Church of the Nazarene on Facebook and Instagram for recent updates, photos, and reminders.</p>
    </article>
  </section>
</div>

<section class="contact-form-heading" id="contact-form">
  <div>
    <p class="content-kicker">Send a message</p>
    <h2>Use the form for general questions or enquiries.</h2>
  </div>
  <p>Your details will be handled carefully and only shared with the people who need them in order to respond appropriately.</p>
</section>
HTML,
    'after_form' => <<<'HTML'
<div class="contact-page contact-page--after-form">
  <section class="contact-directions">
    <div class="contact-directions__copy">
      <p class="content-kicker">Finding us</p>
      <h2>We know the centre can be a little difficult to find.</h2>
      <p>We meet at Millbrook Community Centre on Drumahoe Road. If you are visiting for the first time, Google Maps is a helpful starting point, and you can always message us before you travel.</p>
      <p><a href="https://goo.gl/maps/NnWZoJvMziH2">Open Millbrook Community Centre in Google Maps</a></p>
    </div>
    <div class="contact-map">
      <div class="contact-map__frame">
        <iframe
          title="Map showing Millbrook Community Centre on Drumahoe Road"
          src="https://www.google.com/maps?q=Millbrook%20Community%20Centre%2C%20Drumahoe%20Road%2C%20Millbrook%2C%20Larne%2C%20BT40%202PF&output=embed"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      <div class="contact-map__caption">
        <p class="content-kicker">Map</p>
        <p><strong>Tip for first-time visitors:</strong> if you are unsure where to go, message us before you travel and we can help with directions.</p>
      </div>
    </div>
  </section>

  <section class="contact-privacy">
    <div>
      <p class="content-kicker">Privacy</p>
      <h2>Your message will be handled carefully.</h2>
    </div>
    <p>Information shared through the form is used to respond to your enquiry. It is held in line with Millbrook's data protection responsibilities and restricted to those who need it in order to reply or provide appropriate care.</p>
  </section>
</div>
HTML,
    'form' => [
        'questionSetId' => 2026070101,
        'surveyName' => 'Contact form',
        'submitText' => 'Send message',
        'recipientEmail' => 'info@millbrooknazarene.church',
        'thankyouMsg' => 'Thanks for getting in touch. Someone from the team will get back to you as soon as we can.',
        'questions' => [
            [
                'question' => 'Your name',
                'inputType' => 'field',
                'required' => 1,
                'position' => 1000,
            ],
            [
                'question' => 'Email address',
                'inputType' => 'email',
                'required' => 1,
                'position' => 2000,
                'send_notification_from' => 1,
            ],
            [
                'question' => 'Phone number',
                'inputType' => 'telephone',
                'required' => 0,
                'position' => 3000,
            ],
            [
                'question' => 'What is your enquiry about?',
                'inputType' => 'select',
                'required' => 1,
                'position' => 4000,
                'options' => "General question\nPlanning a visit\nPrayer or pastoral support\nCommunity enquiry\nSomething else",
            ],
            [
                'question' => 'Message',
                'inputType' => 'text',
                'required' => 1,
                'position' => 5000,
                'height' => 7,
            ],
        ],
    ],
];
