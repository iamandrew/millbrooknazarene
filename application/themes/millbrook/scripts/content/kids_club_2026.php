<?php

return [
    'name' => 'Kids Club 2026',
    'description' => 'Register for The Big Picnic, Millbrook Kids Club 2026, 12-14 August.',
    'content' => <<<HTML
<div class="kids-club-2026">
  <div class="kids-club-2026__shell">
    <section class="kids-club-hero" aria-labelledby="kids-club-title">
      <div class="kids-club-hero__copy">
        <p class="kids-club-kicker">Kids Club 2026</p>
        <h1 id="kids-club-title">The Big Picnic</h1>
        <p class="kids-club-hero__lede">Three summer evenings of games, stories, songs, crafts, and time together at Millbrook Community Centre.</p>
        <div class="kids-club-facts" aria-label="Kids Club details">
          <p><strong>Dates</strong><span>12-14 August 2026</span></p>
          <p><strong>Place</strong><span>Millbrook Community Centre</span></p>
          <p><strong>Registration</strong><span>Please complete one form per child.</span></p>
        </div>
      </div>

      <aside class="kids-club-note">
        <p class="kids-club-kicker">Before You Register</p>
        <h2>We would love to welcome your child.</h2>
        <p>This form gives the team the details we need to prepare well and care for children during Kids Club.</p>
        <p>If you have a question before registering, email <a href="mailto:info@millbrooknazarene.co.uk">info@millbrooknazarene.co.uk</a>.</p>
      </aside>
    </section>

    <section class="kids-club-register" aria-labelledby="kids-club-register-title">
      <div class="kids-club-register__intro">
        <p class="kids-club-kicker">Registration Form</p>
        <h2 id="kids-club-register-title">Tell us about your child.</h2>
        <p>Fill in the details below and the registration will be sent to the Kids Club team.</p>
        <p>Your information will be used to manage this registration and help us care for your child safely during the event.</p>
      </div>

      <form class="kids-club-form" method="post" action="/kids-club-2026/register" data-kids-club-form data-google-sheet-endpoint="/kids-club-2026/register">
        <input type="hidden" name="event_name" value="The Big Picnic">
        <input type="hidden" name="event_year" value="2026">
        <input type="hidden" name="event_dates" value="12-14 August 2026">
        <input type="hidden" name="source_page" value="/kids-club-2026">
        <input type="hidden" name="submitted_at" value="" data-kids-club-submitted-at>
        <input type="hidden" name="child_age_on_first_day" value="" data-kids-club-age-input>

        <div class="kids-club-form__field kids-club-form__field--honeypot" aria-hidden="true">
          <label for="kids-club-website">Website</label>
          <input id="kids-club-website" type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <fieldset>
          <legend>Parent or guardian</legend>
          <div class="kids-club-form__grid">
            <label class="kids-club-form__field" for="guardian-name">
              <span>Name</span>
              <input id="guardian-name" type="text" name="guardian_name" autocomplete="name" required>
            </label>

            <label class="kids-club-form__field" for="guardian-email">
              <span>Email address</span>
              <input id="guardian-email" type="email" name="guardian_email" autocomplete="email" required>
            </label>

            <label class="kids-club-form__field" for="guardian-phone">
              <span>Phone number</span>
              <input id="guardian-phone" type="tel" name="guardian_phone" autocomplete="tel" required>
            </label>

            <label class="kids-club-form__field" for="home-address">
              <span>Home address</span>
              <textarea id="home-address" name="home_address" rows="3" autocomplete="street-address" required></textarea>
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Child details</legend>
          <div class="kids-club-form__grid">
            <label class="kids-club-form__field" for="child-name">
              <span>Child's full name</span>
              <input id="child-name" type="text" name="child_name" autocomplete="off" required>
            </label>

            <label class="kids-club-form__field" for="child-dob">
              <span>Date of birth</span>
              <input id="child-dob" type="date" name="date_of_birth" data-kids-club-dob required>
              <small data-kids-club-age-output>Age will appear here once a date is selected.</small>
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Emergency contact</legend>
          <div class="kids-club-form__grid">
            <label class="kids-club-form__field" for="emergency-name">
              <span>Emergency contact name</span>
              <input id="emergency-name" type="text" name="emergency_contact_name" required>
            </label>

            <label class="kids-club-form__field" for="emergency-relationship">
              <span>Relationship to child</span>
              <input id="emergency-relationship" type="text" name="emergency_contact_relationship" required>
            </label>

            <label class="kids-club-form__field" for="emergency-phone">
              <span>Emergency contact phone</span>
              <input id="emergency-phone" type="tel" name="emergency_contact_phone" required>
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Care information</legend>
          <div class="kids-club-form__grid">
            <label class="kids-club-form__field" for="medication">
              <span>Medication</span>
              <textarea id="medication" name="medication" rows="3" placeholder="Please write none if this does not apply." required></textarea>
            </label>

            <label class="kids-club-form__field" for="allergies">
              <span>Allergies</span>
              <textarea id="allergies" name="allergies" rows="3" placeholder="Please write none if this does not apply." required></textarea>
            </label>

            <label class="kids-club-form__field kids-club-form__field--wide" for="additional-needs">
              <span>Additional needs or anything else it would help us to know</span>
              <textarea id="additional-needs" name="additional_needs" rows="4" placeholder="Please write none if this does not apply." required></textarea>
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Permissions</legend>
          <div class="kids-club-form__checks">
            <fieldset class="kids-club-form__radio">
              <legend>Photo and video permission</legend>
              <p>May we use photos or short video clips of your child in Millbrook Church communications?</p>
              <label>
                <input type="radio" name="photo_video_permission" value="Yes" required>
                <span>Yes</span>
              </label>
              <label>
                <input type="radio" name="photo_video_permission" value="No" required>
                <span>No</span>
              </label>
            </fieldset>

            <label class="kids-club-form__check">
              <input type="checkbox" name="first_aid_consent" value="Yes" required>
              <span>I give permission for basic first aid to be given if needed.</span>
            </label>

            <label class="kids-club-form__check">
              <input type="checkbox" name="privacy_acknowledgement" value="Yes" required>
              <span>I understand this information will be used by Millbrook Church to manage this registration and care for my child during Kids Club.</span>
            </label>

            <label class="kids-club-form__check">
              <input type="checkbox" name="future_contact_permission" value="Yes">
              <span>I am happy to hear about future Millbrook children and family events.</span>
            </label>
          </div>
        </fieldset>

        <div class="kids-club-form__actions">
          <button class="kids-club-submit" type="submit" data-kids-club-submit>Send Registration</button>
          <p class="kids-club-form__status" data-kids-club-status role="status" aria-live="polite">Registration details will be sent securely to the Kids Club team.</p>
        </div>
      </form>
    </section>
  </div>
</div>
HTML,
];
