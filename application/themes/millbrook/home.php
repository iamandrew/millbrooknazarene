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
                <h4 class="home-hero__tagline">In the heart of the community, with the community at its heart.</h4>
                <a class="home-hero__button button button--ghost" href="#home-visit">Visit Us</a>
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
                        Millbrook is a warm, Christ-centred, family-friendly church in Larne. We gather each
                        Sunday to worship, learn from God's word, and grow together in community.
                    </p>
                    <div class="hero-actions">
                        <a class="button button--primary" href="#home-visit">Plan Your Visit</a>
                        <a class="button button--ghost" href="/contact">Get in Touch</a>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-one-church" id="home-vision">
        <div class="container home-story__layout">
            <div class="home-story__intro">
                <?php $renderArea('Home Community Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">What to Expect</p>
                    <h2>A church family where you can come as you are.</h2>
                <?php }); ?>
            </div>

            <div class="home-story__copy">
                <?php $renderArea('Home Community Intro', $c, static function (): void { ?>
                    <p>
                        Our Sunday service includes worship, prayer, Bible teaching, and time together as a
                        church family. Whether you have been part of church for years or are simply exploring,
                        you will be very welcome here.
                    </p>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Community Cards', $c, static function (): void { ?>
                <div class="one-church-grid">
                    <article class="community-card community-card--feature">
                        <p class="feature-card__eyebrow">Welcoming atmosphere</p>
                        <h3>Friendly and multi-generational</h3>
                        <p>A relaxed church community where people of all ages can feel at home.</p>
                    </article>
                    <article class="community-card">
                        <p class="feature-card__eyebrow">Worship &amp; Teaching</p>
                        <h3>Centered on Jesus</h3>
                        <p>Contemporary worship and Bible teaching rooted in Scripture and everyday life.</p>
                    </article>
                    <article class="community-card">
                        <p class="feature-card__eyebrow">Children &amp; Families</p>
                        <h3>Families are welcome</h3>
                        <p>We want children and families to feel included, supported, and part of church life.</p>
                    </article>
                    <article class="community-card">
                        <p class="feature-card__eyebrow">Come as you are</p>
                        <h3>No need to know the routine</h3>
                        <p>You do not need to have church figured out before you visit. Just come and we will help.</p>
                    </article>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-story" id="home-story">
        <div class="container home-story__layout">
            <div class="home-story__intro">
                <?php $renderArea('Home Vision Intro', $c, static function (): void { ?>
                    <p class="section-eyebrow">About Millbrook</p>
                    <h2>A local church seeking to follow Jesus and serve our community well.</h2>
                <?php }); ?>
            </div>

            <div class="home-story__copy">
                <?php $renderArea('Home Vision Content', $c, static function (): void { ?>
                    <p>
                        We are a local Church of the Nazarene congregation seeking to follow Jesus, love one
                        another, and serve our community. We want to be a church where people of all ages can
                        encounter God, grow in faith, and find belonging.
                    </p>
                    <a class="text-link" href="/about/who-we-are">Learn More About Us</a>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-life" id="home-ministries">
        <div class="container">
            <div class="section-heading section-heading--center">
                <?php $renderArea('Home Ministries Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Life at Millbrook</p>
                    <h2>Church life that goes beyond Sunday.</h2>
                    <p>Explore a few of the ways people connect, grow, and take part in the life of the church.</p>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Ministries Cards', $c, static function (): void { ?>
                <div class="life-grid">
                    <article class="life-card life-card--kids">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Children &amp; Families</p>
                            <h3>Children and families are an important part of church life at Millbrook.</h3>
                            <p>Find out how we welcome children and support families on Sundays and beyond.</p>
                            <a class="text-link" href="/ministries/children">Learn More</a>
                        </div>
                    </article>
                    <article class="life-card life-card--groups">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Homegroups</p>
                            <h3>Smaller spaces to grow in faith, friendship, and prayer.</h3>
                            <p>Homegroups help people build deeper relationships and stay connected through the week.</p>
                            <a class="text-link" href="/ministries/homegroups">Explore Homegroups</a>
                        </div>
                    </article>
                    <article class="life-card life-card--serve">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Sermons</p>
                            <h3>Catch up on recent messages and teaching.</h3>
                            <p>Listen again, share a sermon, or explore recent teaching from Millbrook.</p>
                            <a class="text-link" href="/resources/sermons">Browse Sermons</a>
                        </div>
                    </article>
                    <article class="life-card life-card--care">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Church Life</p>
                            <h3>Find out more about ministries, events, and ways to get involved.</h3>
                            <p>See more of the gatherings, ministries, and opportunities that shape life at Millbrook.</p>
                            <a class="text-link" href="/ministries">Explore Church Life</a>
                        </div>
                    </article>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-visit" id="home-visit">
        <div class="container home-visit__layout">
            <div class="visit-card">
                <?php $renderArea('Home Visit Card', $c, static function (): void { ?>
                    <p class="section-eyebrow">Plan a visit</p>
                    <h2>We would love to welcome you.</h2>
                    <p class="visit-card__lead">
                        Join us on Sunday at 11:00am at Millbrook Community Centre, Larne. If you have any
                        questions before you come, get in touch and we will be happy to help.
                    </p>

                    <div class="visit-details">
                        <div class="visit-details__item">
                            <span class="visit-details__label">Sunday service</span>
                            <strong>11:00am</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Location</span>
                            <strong>Millbrook Community Centre, Larne</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Need to ask something first?</span>
                            <strong>We are always happy to help before you visit.</strong>
                        </div>
                    </div>

                    <div class="visit-card__actions">
                        <a class="button button--primary" href="/contact">Plan Your Visit</a>
                        <a class="button button--secondary" href="/contact">Contact Us</a>
                    </div>
                <?php }); ?>
            </div>

            <div class="visit-sidebar">
                <?php $renderArea('Home Contact Card', $c, static function (): void { ?>
                    <div class="visit-side-card">
                        <p class="feature-card__eyebrow">What a Sunday feels like</p>
                        <h3>Simple, welcoming, and centred on Jesus.</h3>
                        <p>
                            Our Sundays include worship, prayer, Bible teaching, and time together. If you are
                            new or unsure what to expect, you will not be out of place.
                        </p>
                        <ul class="visit-side-list">
                            <li><strong>Atmosphere</strong><span>Friendly and multi-generational</span></li>
                            <li><strong>Families</strong><span>Children and families are very welcome</span></li>
                            <li><strong>New to church?</strong><span>You can come as you are</span></li>
                        </ul>
                    </div>
                <?php }); ?>

                <?php $renderArea('Home Quick Links Card', $c, static function (): void { ?>
                    <div class="visit-side-card visit-side-card--accent">
                        <p class="feature-card__eyebrow feature-card__eyebrow--light">Get in touch</p>
                        <h3>Questions before you come?</h3>
                        <p>Send us a message and we will be happy to help you feel more at ease before your first visit.</p>
                        <div class="quick-links-list">
                            <a href="/contact">Contact Us</a>
                            <a href="mailto:info@millbrooknazarene.co.uk">Email the Church</a>
                            <a href="/im-new">I'm New</a>
                            <a href="/about/who-we-are">About Millbrook</a>
                        </div>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
