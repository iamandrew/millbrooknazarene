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
    <section class="home-hero" aria-labelledby="home-hero-title">
        <div class="container home-hero__layout">
            <div class="home-hero__brand">
                <img
                    src="<?php echo $themePath; ?>/images/logo-no-sub.svg"
                    alt="Millbrook Church of the Nazarene"
                    class="home-hero__logo"
                >
                <h1 class="home-hero__title" id="home-hero-title">Millbrook Church, Larne</h1>
                <p class="home-hero__statement">In the heart of the community, with the community at its heart.</p>
                <p class="home-hero__sunday">Sundays at <strong>11:00am</strong> &middot; Millbrook Community Centre</p>
                <div class="home-hero__actions">
                    <a class="home-hero__button button button--light" href="/visit-us">Visit Us?</a>
                    <a class="home-hero__link" href="/contact">Ask a question</a>
                </div>
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

    <section class="home-sunday" id="home-sundays">
        <div class="container home-sunday__layout">
            <div class="home-sunday__copy">
                <?php $renderArea('Home Community Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Join us this Sunday</p>
                    <h2 id="home-sunday-title">Sunday worship at 11:00am.</h2>
                <?php }); ?>

                <?php $renderArea('Home Community Intro', $c, static function (): void { ?>
                    <p class="home-sunday__lead">
                        A local church in Larne where people of all ages gather to worship, pray, learn from
                        the Bible, and support one another.
                    </p>
                    <p>
                        We meet every Sunday at Millbrook Community Centre, Larne. Our gathering usually
                        includes worship, prayer, Bible teaching, and time together afterwards. If you are
                        visiting for the first time, we would love to help you feel at ease.
                    </p>
                    <p class="home-sunday__welcome">
                        You do not need to know the words, dress a certain way, or have church all figured out.
                        Come as you are, and we will be glad to welcome you.
                    </p>
                    <p class="home-sunday__new">
                        <strong>New to Millbrook?</strong>
                        Whether you are new to Larne, exploring faith, returning to church, or simply curious,
                        you are welcome to come at your own pace.
                    </p>
                    <div class="home-sunday__actions">
                        <a class="button button--primary" href="/visit-us">Plan Your Visit</a>
                        <a class="text-link" href="/contact">Ask a question before you come</a>
                    </div>
                <?php }); ?>
            </div>

            <div class="home-sunday__details">
                <?php $renderArea('Home Community Cards', $c, static function (): void { ?>
                    <div class="home-sunday__detail">
                        <span>When</span>
                        <strong>11:00am every Sunday</strong>
                        <p>Most people arrive a few minutes early to settle in, say hello, and find a seat.</p>
                    </div>
                    <div class="home-sunday__detail">
                        <span>Where</span>
                        <strong>Millbrook Community Centre, Larne</strong>
                        <p>If you need directions or have access questions, we are happy to help before you come.</p>
                    </div>
                    <div class="home-sunday__detail">
                        <span>What happens</span>
                        <strong>Worship, prayer, Bible teaching</strong>
                        <p>A straightforward Sunday gathering centred on Jesus, Scripture, and time together.</p>
                    </div>
                    <div class="home-sunday__detail">
                        <span>Families</span>
                        <strong>Children are welcome</strong>
                        <p>Children are a valued part of church life, and tea and coffee usually follow the service.</p>
                    </div>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-life" id="home-community">
        <div class="container home-life__layout">
            <div class="home-life__copy">
                <?php $renderArea('Home Ministries Heading', $c, static function (): void { ?>
                    <p class="section-eyebrow">Church life</p>
                    <h2 id="home-life-title">Church life through the week.</h2>
                    <p>Alongside Sunday worship, people connect through homegroups, prayer, children’s activities, shared meals, and everyday care.</p>
                    <a class="text-link" href="/community">See church life</a>
                <?php }); ?>
            </div>

            <div class="home-life__rhythms">
                <?php $renderArea('Home Ministries Cards', $c, static function (): void { ?>
                    <a class="home-life__rhythm" href="/visit-us">
                        <span>Worship &amp; prayer</span>
                        <strong>Gathering to worship God and pray together.</strong>
                        <p>Sunday worship, prayer, Scripture, and time together shape the ordinary life of the church.</p>
                    </a>
                    <a class="home-life__rhythm" href="/community/children">
                        <span>Children &amp; families</span>
                        <strong>Helping children feel welcome, safe, and included.</strong>
                        <p>We want families to feel supported when they arrive and able to take part at their own pace.</p>
                    </a>
                    <a class="home-life__rhythm" href="/community/homegroups">
                        <span>Homegroups</span>
                        <strong>Smaller spaces for friendship, prayer, and opening the Bible.</strong>
                        <p>Homegroups help people build relationships and keep growing in faith beyond Sunday morning.</p>
                    </a>
                    <a class="home-life__rhythm" href="/community/whats-on">
                        <span>Shared life</span>
                        <strong>Meals, ministry gatherings, and everyday support.</strong>
                        <p>Church life includes simple ways to belong, serve, and encourage one another through the week.</p>
                    </a>
                <?php }); ?>
            </div>
        </div>
    </section>

    <section class="home-visit" id="home-whats-on">
        <div class="container home-visit__layout">
            <div class="visit-card">
                <?php $renderArea('Home Visit Card', $c, static function (): void { ?>
                    <p class="section-eyebrow">What’s On</p>
                    <h2 id="home-whats-on-title">Ways to connect.</h2>
                    <p class="visit-card__lead">
                        Regular rhythms across the week help people pray, connect, and grow together.
                    </p>

                    <div class="visit-details">
                        <div class="visit-details__item">
                            <span class="visit-details__label">Sunday</span>
                            <strong>Worship at 11:00am</strong>
                            <p>Join us each Sunday for worship, prayer, Bible teaching, and time together afterwards.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Midweek</span>
                            <strong>Homegroups, prayer, and shared life</strong>
                            <p>Smaller gatherings through the week help people build friendships and keep growing in faith.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Families</span>
                            <strong>Children and families are welcome</strong>
                            <p>Children are a valued part of church life, with support for families and age-appropriate opportunities to belong.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Recent teaching</span>
                            <strong>Catch up on sermons and Bible teaching</strong>
                            <p>Listen back to recent messages from Millbrook before you visit or during the week.</p>
                        </div>
                    </div>

                    <div class="visit-card__actions">
                        <a class="button button--primary" href="/community/whats-on">See What’s On</a>
                        <a class="text-link" href="/resources/sermons">Listen to latest sermons</a>
                    </div>
                <?php }); ?>
            </div>

            <aside class="visit-side-card">
                <?php $renderArea('Home Contact Card', $c, static function (): void { ?>
                    <p class="section-eyebrow">Got a question?</p>
                    <h2>We would be glad to help.</h2>
                    <p>
                        If you are wondering about Sundays, children, accessibility, or what to expect when you arrive,
                        please get in touch.
                    </p>
                    <p class="visit-side-card__detail">Millbrook Community Centre, Larne</p>
                    <a class="button button--secondary" href="/contact">Contact Us</a>
                <?php }); ?>
            </aside>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
