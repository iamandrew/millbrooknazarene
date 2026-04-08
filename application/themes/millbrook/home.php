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
                    src="<?php echo $themePath; ?>/images/logo-no-sub.png"
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
                    <p class="section-eyebrow">God-centred, family-focused, community-based</p>
                    <p class="hero-lead">
                        Millbrook Church of the Nazarene is a contemporary, multi-generational church
                        passionate about worshipping God and helping people live a real Christian life
                        in the real world.
                    </p>
                    <div class="hero-actions">
                        <a class="button button--primary" href="#home-visit">Visit Us</a>
                        <a class="button button--ghost" href="#home-story">Our Vision</a>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-story" id="home-story">
        <div class="container home-story__layout">
            <div class="home-story__intro">
                <?php $renderArea('Home Vision Intro', $c, static function (): void { ?>
                    <p class="section-eyebrow">Our vision</p>
                    <h2>Walking with Jesus and loving our neighbours.</h2>
                <?php }); ?>
            </div>

            <div class="home-story__copy">
                <?php $renderArea('Home Vision Content', $c, static function (): void { ?>
                    <p>
                        We love Millbrook and the surrounding community. As a church family, we want to live
                        as a blessing right here by practicing worship, friendship, generosity, and prayer.
                    </p>
                    <p>
                        Our life together is shaped by Jesus, Scripture, and the everyday call to love our
                        neighbours well. We want church to feel rooted, hope-filled, and open to anyone who
                        is looking for faith and community.
                    </p>
                    <a class="text-link" href="/contact">Our Vision</a>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-one-church" id="home-vision">
        <div class="container">
            <div class="section-heading section-heading--center">
                <?php $renderArea('Home Community Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">One church</p>
                    <h2>Three shared rhythms.</h2>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Community Cards', $c, static function (): void { ?>
                <div class="one-church-grid">
                    <article class="community-card community-card--feature">
                        <p class="feature-card__eyebrow">Gather</p>
                        <h3>Sunday Worship</h3>
                        <p>Join us at 11:00am for worship, prayer, and biblical teaching.</p>
                    </article>
                    <article class="community-card">
                        <p class="feature-card__eyebrow">Grow</p>
                        <h3>Home Group</h3>
                        <p>Friendship, Bible study, and prayer through the week in a smaller setting.</p>
                    </article>
                    <article class="community-card">
                        <p class="feature-card__eyebrow">Serve</p>
                        <h3>Life Together</h3>
                        <p>Supporting one another and serving our community with practical love.</p>
                    </article>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-featured" id="home-teaching">
        <div class="container">
            <div class="home-featured__layout">
                <div class="home-featured__main">
                    <?php $renderArea('Home Sermons', $c, static function () use ($themePath): void { ?>
                        <p class="section-eyebrow">Featured teaching</p>
                        <h2>Watch or listen to a recent message.</h2>

                        <article class="teaching-card">
                            <div class="teaching-card__media">
                                <img
                                    src="<?php echo $themePath; ?>/images/placeholder-640x360.png"
                                    alt="Featured sermon placeholder"
                                >
                            </div>
                            <div class="teaching-card__body">
                                <p class="teaching-card__meta">Recent sermon</p>
                                <h3>Take Sunday with you into the rest of the week.</h3>
                                <p>
                                    Catch up on recent sermons, revisit Sunday's message, or share a teaching
                                    with someone who needs encouragement.
                                </p>
                                <a class="text-link" href="/resources/sermons">Browse Sermons</a>
                            </div>
                        </article>
                    <?php }); ?>
                </div>

                <aside class="home-featured__aside">
                    <?php $renderArea('Home Connect', $c, static function (): void { ?>
                        <div class="info-card info-card--accent" id="home-contact">
                            <p class="section-eyebrow section-eyebrow--light">Visit us</p>
                            <h3>Come and see what church life feels like at Millbrook.</h3>
                            <p>
                                If you are planning a first visit, have questions, or want someone to welcome
                                you personally, we would love to help.
                            </p>
                            <div class="stacked-links">
                                <a class="button button--light" href="/contact">Contact Millbrook</a>
                                <a class="text-link text-link--light" href="mailto:info@millbrooknazarene.co.uk">
                                    info@millbrooknazarene.co.uk
                                </a>
                            </div>
                        </div>
                    <?php }); ?>
                </aside>
            </div>
        </div>
    </section>

    <section class="home-next-steps" id="home-next-steps">
        <div class="container">
            <?php $renderArea('Home Next Steps', $c, static function (): void { ?>
                <div class="section-heading section-heading--center section-heading--light">
                    <p class="section-eyebrow section-eyebrow--light">Take your next step</p>
                    <h2>Clear pathways into life at Millbrook.</h2>
                    <p>
                        This follows the reference site's "next step" pattern more directly: simple cards
                        that help people move toward connection, teaching, and belonging.
                    </p>
                </div>

                <div class="step-grid">
                    <article class="step-card">
                        <p class="step-card__label">I'm New</p>
                        <h3>Find out what to expect when you join us.</h3>
                        <a class="text-link text-link--light" href="/contact">Plan a Visit</a>
                    </article>
                    <article class="step-card">
                        <p class="step-card__label">Vision</p>
                        <h3>Discover the heart behind life at Millbrook.</h3>
                        <a class="text-link text-link--light" href="#home-story">Read More</a>
                    </article>
                    <article class="step-card">
                        <p class="step-card__label">Teachings</p>
                        <h3>Catch up on sermons and keep growing through the week.</h3>
                        <a class="text-link text-link--light" href="/resources/sermons">Watch a Teaching</a>
                    </article>
                    <article class="step-card">
                        <p class="step-card__label">Community</p>
                        <h3>See the regular rhythms that shape our church family.</h3>
                        <a class="text-link text-link--light" href="#home-vision">Explore Church Life</a>
                    </article>
                    <article class="step-card">
                        <p class="step-card__label">Prayer</p>
                        <h3>Reach out if you would value prayer or pastoral support.</h3>
                        <a class="text-link text-link--light" href="/contact">Request Prayer</a>
                    </article>
                    <article class="step-card">
                        <p class="step-card__label">Giving</p>
                        <h3>Support the ministry and mission of Millbrook.</h3>
                        <a class="text-link text-link--light" href="/giving">Give Online</a>
                    </article>
                </div>
            <?php }); ?>
        </div>
    </section>

    <section class="home-life" id="home-ministries">
        <div class="container">
            <div class="section-heading section-heading--center">
                <?php $renderArea('Home Ministries Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Life at Millbrook</p>
                    <h2>Find your place in the life of the church.</h2>
                    <p>
                        The reference site keeps the lower half of the homepage practical and people-focused.
                        This section does the same by surfacing a few key pathways into community.
                    </p>
                <?php }); ?>
            </div>

            <?php $renderArea('Home Ministries Cards', $c, static function (): void { ?>
                <div class="life-grid">
                    <article class="life-card life-card--kids">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Children</p>
                            <h3>Space for families and younger generations.</h3>
                            <p>
                                We want children and families to experience church as a place of welcome,
                                joy, and faith-filled growth.
                            </p>
                            <a class="text-link" href="/contact">Learn More</a>
                        </div>
                    </article>
                    <article class="life-card life-card--groups">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Groups</p>
                            <h3>Grow through friendship, prayer, and Scripture.</h3>
                            <p>
                                Home Group is one of the main ways people are known, supported, and encouraged
                                through the week.
                            </p>
                            <a class="text-link" href="#home-vision">Explore Groups</a>
                        </div>
                    </article>
                    <article class="life-card life-card--serve">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Serving</p>
                            <h3>Use your gifts to bless others.</h3>
                            <p>
                                Serving is one of the best ways to get connected while helping church life
                                feel warm, generous, and welcoming.
                            </p>
                            <a class="text-link" href="/contact">Start Serving</a>
                        </div>
                    </article>
                    <article class="life-card life-card--care">
                        <div class="life-card__media"></div>
                        <div class="life-card__body">
                            <p class="feature-card__eyebrow">Care</p>
                            <h3>Prayer, support, and pastoral connection.</h3>
                            <p>
                                If you are carrying something heavy or simply want someone to pray with you,
                                our church family would love to walk with you.
                            </p>
                            <a class="text-link" href="/contact">Request Prayer</a>
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
                    <h2>Come and join us in person.</h2>
                    <p class="visit-card__lead">
                        We gather on Sundays at Millbrook Community Centre for worship, prayer, teaching, and
                        time together as a church family.
                    </p>

                    <div class="visit-details">
                        <div class="visit-details__item">
                            <span class="visit-details__label">Sunday worship</span>
                            <strong>11:00am</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Home Group</span>
                            <strong>Every other Thursday, 7:30pm</strong>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Location</span>
                            <strong>Millbrook Community Centre, Drumahoe Road, Millbrook</strong>
                        </div>
                    </div>

                    <div class="visit-card__actions">
                        <a class="button button--primary" href="/contact">Plan Your Visit</a>
                        <a class="button button--secondary" href="mailto:info@millbrooknazarene.co.uk">Email Us</a>
                    </div>
                <?php }); ?>
            </div>

            <div class="visit-sidebar">
                <?php $renderArea('Home Contact Card', $c, static function (): void { ?>
                    <div class="visit-side-card">
                        <p class="feature-card__eyebrow">Contact</p>
                        <h3>We would love to hear from you.</h3>
                        <p>
                            Whether you are new, returning, or just looking for more information, we are
                            always happy to help.
                        </p>
                        <ul class="visit-side-list">
                            <li><strong>Email</strong><span>info@millbrooknazarene.co.uk</span></li>
                            <li><strong>Visit</strong><span>Millbrook Community Centre, Drumahoe Road</span></li>
                            <li><strong>Give</strong><span>Support the mission and ministry of Millbrook</span></li>
                        </ul>
                    </div>
                <?php }); ?>

                <?php $renderArea('Home Quick Links Card', $c, static function (): void { ?>
                    <div class="visit-side-card visit-side-card--accent">
                        <p class="feature-card__eyebrow feature-card__eyebrow--light">Quick links</p>
                        <div class="quick-links-list">
                            <a href="/resources/sermons">Watch a Teaching</a>
                            <a href="/giving">Give Online</a>
                            <a href="/contact">Ask a Question</a>
                            <a href="#home-vision">Our Vision</a>
                        </div>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-cta">
        <div class="container">
            <?php $renderArea('Home CTA', $c, static function (): void { ?>
                <div class="cta-card">
                    <div class="cta-card__content">
                        <p class="section-eyebrow section-eyebrow--light">Come and say hello</p>
                        <h2>We would love to welcome you to Millbrook.</h2>
                        <p>
                            Whether you want to join us on Sunday, ask a question, or find out how to get
                            involved, there is room for you here.
                        </p>
                    </div>
                    <div class="cta-card__actions">
                        <a class="button button--light" href="/contact">Get in Touch</a>
                        <a class="button button--ghost-light" href="#home-gatherings">Service Times</a>
                    </div>
                </div>
            <?php }); ?>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
