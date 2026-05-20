<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
$themePath = $view->getThemePath();

$renderArea = static function (string $areaName, $c, callable $fallback): void {
    $area = new Area($areaName);
    if ($area->getTotalBlocksInArea($c) > 0) {
        $area->display($c);
        return;
    }

    $fallback();
};
?>

<main id="main-content" class="home-page">
    <section
        class="home-hero"
    >
        <div class="container home-hero__layout">
            <div class="home-hero__brand">
                <img
                    src="<?php echo $themePath; ?>/images/logo-no-sub.svg"
                    alt="Millbrook Church of the Nazarene"
                    class="home-hero__logo"
                >
                <h1 class="home-hero__tagline" id="home-hero-title">In the heart of the community, with the community at its heart.</h1>
                <a class="home-hero__button button button--ghost" href="/visit-us">Visit Us?</a>
            </div>
            <div class="home-hero__media" id="home-gatherings">
                <div class="hero-visual">
                    <div
                        class="hero-image-card"
                        aria-hidden="true"
                        style="--hero-image: url('<?php echo $themePath; ?>/images/hero.png');"
                    ></div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-intro" id="home-intro" aria-labelledby="home-hero-title">
        <div class="container">
            <div class="home-intro__content">
                <?php $renderArea('Home Hero Content', $c, static function (): void { ?>
                    <p class="section-eyebrow">Millbrook Church, Larne</p>
                    <p class="hero-lead">
                        A local church in Larne where people of all ages gather to worship, pray, learn from
                        the Bible, and support one another.
                    </p>
                    <div class="hero-actions">
                        <a class="button button--primary" href="/visit-us">Visit Us?</a>
                        <a class="button button--ghost" href="/visit-us">Join Us This Sunday</a>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-one-church" id="home-sundays">
        <div class="container home-story__layout">
            <div class="home-story__intro">
                <?php $renderArea('Home Community Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Join us this Sunday</p>
                    <h2>Sundays at 11:00am in Larne.</h2>
                <?php }); ?>
            </div>

            <div class="home-story__copy">
                <?php $renderArea('Home Community Intro', $c, static function (): void { ?>
                    <p>
                        We meet every Sunday at Millbrook Community Centre, Larne. Our gathering usually
                        includes worship, prayer, Bible teaching, and time together afterwards. If you are
                        visiting for the first time, we would love to help you feel at ease.
                    </p>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Community Cards', $c, static function (): void { ?>
                <div class="home-sunday-panel">
                    <div class="home-sunday-panel__summary">
                        <div class="home-sunday-panel__heading">
                            <p class="feature-card__eyebrow">Your first Sunday</p>
                            <h3>Simple, welcoming, and easy to step into.</h3>
                        </div>
                        <p class="home-sunday-panel__lead">
                            You do not need to know the words, dress a certain way, or have church all figured out.
                            Come as you are, and we will be glad to welcome you.
                        </p>
                        <div class="home-sunday-panel__actions">
                            <a class="button button--primary" href="/visit-us">What to Expect</a>
                            <a class="button button--ghost" href="/contact">Ask a Question</a>
                        </div>
                    </div>

                    <div class="one-church-grid">
                        <article class="community-card community-card--feature community-card--lead">
                            <p class="feature-card__eyebrow">Sunday service</p>
                            <h3>11:00am every Sunday</h3>
                            <p>Most people arrive a few minutes early to settle in, say hello, and find a seat.</p>
                        </article>
                        <article class="community-card community-card--lead community-card--location">
                            <p class="feature-card__eyebrow">Location</p>
                            <h3>Millbrook Community Centre, Larne</h3>
                            <p>If you need directions or have access questions, we are happy to help before you come.</p>
                        </article>
                        <article class="community-card">
                            <p class="feature-card__eyebrow">What happens</p>
                            <h3>Worship, prayer, Bible teaching</h3>
                            <p>A straightforward Sunday gathering centred on Jesus, Scripture, and time together.</p>
                        </article>
                        <article class="community-card">
                            <p class="feature-card__eyebrow">Children welcome</p>
                            <h3>Families can relax</h3>
                            <p>Children are a valued part of church life, and tea and coffee usually follow the service.</p>
                        </article>
                    </div>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-story home-story--new" id="home-new">
        <div class="container home-story__layout">
            <div class="home-story__intro">
                <?php $renderArea('Home Vision Intro', $c, static function (): void { ?>
                    <p class="section-eyebrow">New to Millbrook?</p>
                    <h2>You do not need to have it all figured out before you come.</h2>
                <?php }); ?>
            </div>

            <div class="home-story__copy">
                <?php $renderArea('Home Vision Content', $c, static function (): void { ?>
                    <p>
                        We know visiting a church can feel like a big step, especially if you are not sure what
                        to expect. Whether you are new to Larne, exploring faith, returning to church, or simply
                        curious, we want to help you feel informed, comfortable, and welcome at your own pace.
                    </p>
                    <a class="text-link" href="/visit-us">Plan Your Visit</a>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-life" id="home-community">
        <div class="container">
            <div class="section-heading section-heading--center">
                <?php $renderArea('Home Ministries Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Church Life</p>
                    <h2>More than a Sunday service.</h2>
                    <p>Millbrook exists to be a faithful, loving presence in our community through worship, prayer, care, and everyday church life.</p>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Ministries Cards', $c, static function (): void { ?>
                <div class="life-grid">
                    <article class="life-card life-card--kids">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Worship &amp; Prayer</p>
                            <h3>Sunday worship and prayer shape the life of our church.</h3>
                            <p>We gather to worship God, listen to Scripture, and pray together in ordinary life.</p>
                            <a class="text-link" href="/visit-us">Plan Your Visit</a>
                        </div>
                    </article>
                    <article class="life-card life-card--groups">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Children &amp; Families</p>
                            <h3>Children and families are a valued part of church life at Millbrook.</h3>
                            <p>We want children to feel welcome, safe, included, and supported when they arrive.</p>
                            <a class="text-link" href="/community/children">Explore children &amp; families</a>
                        </div>
                    </article>
                    <article class="life-card life-card--serve">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Community life</p>
                            <h3>There is more to church than Sunday morning.</h3>
                            <p>Homegroups, shared meals, ministry gatherings, and everyday support all help people belong.</p>
                            <a class="text-link" href="/community">See church life</a>
                        </div>
                    </article>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-visit" id="home-whats-on">
        <div class="container home-visit__layout">
            <div class="visit-card">
                <?php $renderArea('Home Visit Card', $c, static function (): void { ?>
                    <p class="section-eyebrow">What’s On</p>
                    <h2>A few simple ways to connect this month.</h2>
                    <p class="visit-card__lead">
                        Alongside Sunday worship, there are regular gatherings, groups, and recent teaching
                        that help people pray, connect, and grow together.
                    </p>

                    <div class="visit-details">
                        <div class="visit-details__item">
                            <span class="visit-details__label">Sunday</span>
                            <strong>Worship at 11:00am</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Midweek</span>
                            <strong>Homegroups, prayer, and ministry gatherings through the week</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Recent teaching</span>
                            <strong>Catch up on recent sermons and Bible teaching from Millbrook</strong>
                        </div>
                    </div>

                    <div class="visit-card__actions">
                        <a class="button button--primary" href="/community/whats-on">See What’s On</a>
                        <a class="button button--secondary" href="/resources/sermons">Latest Sermons</a>
                    </div>
                <?php }); ?>
            </div>

            <div class="visit-sidebar">
                <?php $renderArea('Home Contact Card', $c, static function (): void { ?>
                    <div class="visit-side-card">
                        <p class="feature-card__eyebrow">Got a question?</p>
                        <h3>Easy next steps for a first visit.</h3>
                        <p>
                            If you are wondering about Sundays, children, accessibility, or simply what to
                            expect when you arrive, please get in touch.
                        </p>
                        <ul class="visit-side-list">
                            <li><strong>Visit Us?</strong><span>Everything you need for a first visit</span></li>
                            <li><strong>Contact</strong><span>Ask a question before you come</span></li>
                            <li><strong>Find us</strong><span>Millbrook Community Centre, Larne</span></li>
                            <li><strong>Recent teaching</strong><span>Catch up with the latest sermons and messages</span></li>
                        </ul>

                        <div class="quick-links-list quick-links-list--compact">
                            <a href="/visit-us">Visit Us?</a>
                            <a href="/contact">Contact Us</a>
                            <a href="/resources/sermons">Latest Sermons</a>
                        </div>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
