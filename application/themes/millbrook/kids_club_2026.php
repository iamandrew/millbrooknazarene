<?php
defined('C5_EXECUTE') or die('Access Denied.');

$this->inc('elements/header.php');
$themePath = $this->getThemePath();
?>

<main id="main-content" class="site-main site-main--kids-club">
    <div class="kids-club-event">
        <div class="kids-club-event__scene" aria-hidden="true">
            <img class="kids-club-event__hills" src="<?php echo h($themePath); ?>/images/kids-club-2026/scene-hills.webp" alt="">
            <img class="kids-club-event__kite" src="<?php echo h($themePath); ?>/images/kids-club-2026/scene-kite.webp" alt="">
        </div>

        <section class="kids-club-event__hero" aria-labelledby="kids-club-title">
            <div class="kids-club-event__hero-inner">
                <h1 id="kids-club-title" class="visually-hidden">The Big Picnic Kids Club</h1>
                <img class="kids-club-event__logo" src="<?php echo h($themePath); ?>/images/kids-club-2026/scene-logo.webp" alt="The Big Picnic Kids Club" width="1200" height="675" fetchpriority="high">
            </div>
        </section>

        <section class="kids-club-registration" id="kids-club-registration" aria-labelledby="kids-club-registration-title">
            <div class="kids-club-registration__inner">
                <div class="kids-club-registration__panel">
                    <img class="kids-club-registration__signpost" src="<?php echo h($themePath); ?>/images/kids-club-2026/scene-signpost.webp" alt="" aria-hidden="true">
                    <div class="kids-club-registration__heading" data-kids-club-registration-heading>
                        <h2 id="kids-club-registration-title">Register your child</h2>
                        <p>Please complete one form for each child.</p>
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

                    <fieldset class="kids-club-form__section kids-club-form__section--coral">
                        <legend>Parent or guardian</legend>
                        <div class="kids-club-form__grid">
                            <label class="kids-club-form__field" for="guardian-name"><span>Your Name</span><input id="guardian-name" type="text" name="guardian_name" autocomplete="name" required></label>
                            <label class="kids-club-form__field" for="guardian-email"><span>Email address</span><input id="guardian-email" type="email" name="guardian_email" autocomplete="email" required></label>
                            <label class="kids-club-form__field" for="guardian-phone"><span>Phone number</span><input id="guardian-phone" type="tel" name="guardian_phone" autocomplete="tel" required></label>
                            <label class="kids-club-form__field kids-club-form__field--wide" for="home-address"><span>Home address</span><textarea id="home-address" name="home_address" rows="3" autocomplete="street-address" required></textarea></label>
                        </div>
                    </fieldset>

                    <fieldset class="kids-club-form__section kids-club-form__section--purple">
                        <legend>Child details</legend>
                        <div class="kids-club-form__grid">
                            <label class="kids-club-form__field" for="child-name"><span>Child's full name</span><input id="child-name" type="text" name="child_name" autocomplete="off" required></label>
                            <label class="kids-club-form__field" for="child-dob"><span>Date of birth</span><input id="child-dob" type="date" name="date_of_birth" data-kids-club-dob required><small data-kids-club-age-output>Age will appear here once a date is selected.</small></label>
                        </div>
                    </fieldset>

                    <fieldset class="kids-club-form__section kids-club-form__section--sky">
                        <legend>Emergency contact</legend>
                        <div class="kids-club-form__grid">
                            <label class="kids-club-form__field" for="emergency-name"><span>Emergency contact name</span><input id="emergency-name" type="text" name="emergency_contact_name" required></label>
                            <label class="kids-club-form__field" for="emergency-relationship"><span>Relationship to child</span><input id="emergency-relationship" type="text" name="emergency_contact_relationship" required></label>
                            <label class="kids-club-form__field" for="emergency-phone"><span>Emergency contact phone</span><input id="emergency-phone" type="tel" name="emergency_contact_phone" required></label>
                        </div>
                    </fieldset>

                    <fieldset class="kids-club-form__section kids-club-form__section--lime">
                        <legend>Care information</legend>
                        <div class="kids-club-form__grid">
                            <label class="kids-club-form__field" for="medication"><span>Medication</span><textarea id="medication" name="medication" rows="3" placeholder="Please write none if this does not apply." required></textarea></label>
                            <label class="kids-club-form__field" for="allergies"><span>Allergies</span><textarea id="allergies" name="allergies" rows="3" placeholder="Please write none if this does not apply." required></textarea></label>
                            <label class="kids-club-form__field kids-club-form__field--wide" for="additional-needs"><span>Additional needs or anything else it would help us to know</span><textarea id="additional-needs" name="additional_needs" rows="4" placeholder="Please write none if this does not apply." required></textarea></label>
                        </div>
                    </fieldset>

                    <fieldset class="kids-club-form__section kids-club-form__section--coral">
                        <legend>Permissions</legend>
                        <div class="kids-club-form__checks">
                            <fieldset class="kids-club-form__radio">
                                <legend>Photo and video permission</legend>
                                <p>May we use photos or short video clips of your child in Millbrook Church communications?<br /><small>Saying no is completely fine and will not affect your child's place.</small></p>
                                <label><input type="radio" name="photo_video_permission" value="Yes" required><span>Yes</span></label>
                                <label><input type="radio" name="photo_video_permission" value="No" required><span>No</span></label>
                            </fieldset>
                            <label class="kids-club-form__check"><input type="checkbox" name="first_aid_consent" value="Yes" required><span>I give permission for basic first aid to be given if needed.</span></label>
                            <label class="kids-club-form__check"><input type="checkbox" name="privacy_acknowledgement" value="Yes" required><span>I understand this information will be used by Millbrook Church to manage this registration and care for my child during Kids Club.</span></label>
                            <label class="kids-club-form__check"><input type="checkbox" name="future_contact_permission" value="Yes"><span>I am happy to hear about future Millbrook children and family events.</span></label>
                        </div>
                    </fieldset>

                    <div class="kids-club-form__actions">
                        <button class="kids-club-submit" type="submit" data-kids-club-submit>Send registration</button>
                        <p class="kids-club-form__status" data-kids-club-status role="status" aria-live="polite">Registration details will be sent securely to the Kids Club team.</p>
                    </div>
                    </form>

                    <section class="kids-club-registration__confirmation" data-kids-club-confirmation hidden tabindex="-1" aria-labelledby="kids-club-confirmation-title">
                        <p class="kids-club-registration__confirmation-kicker">Registration received</p>
                        <h2 id="kids-club-confirmation-title">You are all set for The Big Picnic.</h2>
                        <p>Thanks. We have received this registration and passed it on to the Kids Club team.</p>
                        <p data-kids-club-confirmation-email>Look out for a confirmation email shortly. If it does not arrive within a few minutes, please check your spam folder, then email <a href="mailto:info@millbrooknazarene.church">info@millbrooknazarene.church</a>.</p>
                        <button class="kids-club-registration__another" type="button" data-kids-club-register-another>Register another child</button>
                    </section>
                </div>
            </div>
        </section>
    </div>
</main>

<?php $this->inc('elements/footer.php'); ?>
