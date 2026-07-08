<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
require_once __DIR__ . '/elements/theme_assets.php';
$themePath = $view->getThemePath();
$homeHeroMarkup = millbrook_default_hero_image_markup($themePath);
$homeLogoUrl = millbrook_theme_asset_url($themePath, 'images/logo-no-sub.svg');
$homeLifeImageUrl = millbrook_theme_asset_url($themePath, 'images/content/general/home-church-life.jpeg');
$homeWelcomeImageUrl = millbrook_theme_asset_url($themePath, 'images/content/general/home-welcome-coffee.jpeg');

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
    <section class="page-hero page-hero--has-image page-hero--home home-hero" aria-labelledby="home-hero-title">
        <div class="container page-hero__layout">
            <div class="page-hero__brand">
                <img
                    src="<?php echo h($homeLogoUrl); ?>"
                    alt="Millbrook Church of the Nazarene"
                    class="page-hero__logo"
                    width="2361"
                    height="490"
                >
                <h1 class="home-hero__title" id="home-hero-title">Millbrook Church, Larne</h1>
                <p class="page-hero__description page-hero__description--home">A warm, family-friendly, Christ-centred church in Larne, living out faith in practical care for others.</p>
                <p class="page-hero__meta page-hero__meta--home">Sundays at <strong>11:00am</strong> &middot; Millbrook Community Centre</p>
                <div class="page-hero__actions page-hero__actions--home">
                    <a class="page-hero__button button button--light" href="/visit-us">Visit Us?</a>
                    <a class="page-hero__link" href="/community/whats-on">What’s On</a>
                </div>
            </div>
            <div class="page-hero__media" id="home-gatherings">
                <div class="hero-visual">
                    <div
                        class="hero-image-card"
                        aria-hidden="true"
                    ><?php echo $homeHeroMarkup; ?></div>
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
                        You would be very welcome at Millbrook. We gather every Sunday to worship, pray,
                        learn from the Bible, and support one another.
                    </p>
                    <p>
                        We meet at Millbrook Community Centre in Larne. Our Sunday service is relaxed and
                        centred on Jesus, with worship, prayer, Bible teaching from our pastoral team or a
                        guest speaker, and time together afterwards.
                    </p>
                    <p class="home-sunday__welcome">
                        You do not need to know the words, dress a certain way, or have church figured out.
                        Come as you are.
                    </p>
                    <p class="home-sunday__new">
                        <strong>New to Millbrook?</strong>
                        Whether you are new to Larne, exploring faith, returning to church, or just quietly
                        curious, you are welcome to come at your own pace. If it would help, contact us before
                        you come and we can look out for you.
                    </p>
                    <div class="home-sunday__actions">
                        <a class="button button--primary" href="/visit-us">Plan your visit</a>
                        <a class="text-link" href="/resources/sermons">Listen to recent teaching</a>
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
                        <span>Prayer</span>
                        <strong>10:45am before the service</strong>
                        <p>There is a short time of prayer in the committee room before worship begins.</p>
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
                        <p>Children of all ages are welcome, with creche and Sunday School usually available during the service.</p>
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
                    <h2 id="home-life-title">Faith lived out together.</h2>
                    <p>Church life is more than a Sunday service. We make space for prayer, youth, families, friendship, care, and practical involvement in the local community.</p>
                    <a class="text-link" href="/community">Explore church life</a>
                <?php }); ?>

                <figure class="home-life__photo">
                    <img
                        src="<?php echo h($homeLifeImageUrl); ?>"
                        alt="People chatting at Millbrook Church."
                        width="1600"
                        height="900"
                        loading="lazy"
                        decoding="async"
                    >
                </figure>
            </div>

            <div class="home-life__rhythms">
                <?php $renderArea('Home Ministries Cards', $c, static function (): void { ?>
                    <a class="home-life__rhythm" href="/visit-us">
                        <span>Sunday worship &amp; prayer</span>
                        <strong>Gathering to worship God and pray together.</strong>
                        <p>Sunday worship at 11:00am, prayer before the service, Scripture, and time together shape our week.</p>
                    </a>
                    <a class="home-life__rhythm" href="/visit-us">
                        <span>Children &amp; families</span>
                        <strong>Helping children feel welcome, safe, and included.</strong>
                        <p>Children of all ages are welcome, with creche and Sunday School usually available during the service.</p>
                    </a>
                    <a class="home-life__rhythm" href="/community/youth">
                        <span>Youth</span>
                        <strong>A relaxed space for secondary school age young people.</strong>
                        <p>Youth meets on Sunday evenings with snacks, games, teaching, trips, and time to belong.</p>
                    </a>
                    <a class="home-life__rhythm" href="/community/whats-on">
                        <span>Community life</span>
                        <strong>Practical love, friendship, and support through the week.</strong>
                        <p>We are actively involved locally, making space for people to connect, find support, and serve others.</p>
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
                    <h2 id="home-whats-on-title">A few ways to begin.</h2>
                    <p class="visit-card__lead">
                        If you are wondering where to start, Sunday worship is always a good first step.
                        These regular rhythms help people pray, connect, and grow together.
                    </p>

                    <div class="visit-details">
                        <div class="visit-details__item">
                            <span class="visit-details__label">Sunday</span>
                            <strong>Worship at 11:00am</strong>
                            <p>Join us each Sunday for worship, prayer, Bible teaching, and time together afterwards.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Prayer</span>
                            <strong>Prayer before church at 10:45am</strong>
                            <p>You are welcome to join the short prayer time before the Sunday service.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Youth</span>
                            <strong>Sunday evenings for secondary school age young people</strong>
                            <p>A relaxed space with snacks, games, teaching, trips, and time together.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Homegroups</span>
                            <strong>Sunday and Wednesday evenings in homes</strong>
                            <p>Smaller spaces for friendship, Bible discussion, support, and prayer.</p>
                        </div>
                        <div class="visit-details__item">
                            <span class="visit-details__label">Community</span>
                            <strong>Community Cafe, Cafe Fit, and local events</strong>
                            <p>Simple spaces through the week for connection, wellbeing, friendship, and support.</p>
                        </div>
                    </div>

                    <div class="visit-card__actions">
                        <a class="button button--primary" href="/community/whats-on">See What’s On</a>
                        <a class="text-link" href="/resources/sermons">Listen to latest sermons</a>
                    </div>
                <?php }); ?>
            </div>

            <aside class="visit-side-card">
                <figure class="visit-side-card__photo">
                    <img
                        src="<?php echo h($homeWelcomeImageUrl); ?>"
                        alt="Tea and coffee being served at Millbrook Church."
                        width="1200"
                        height="675"
                        loading="lazy"
                        decoding="async"
                    >
                </figure>

                <?php $renderArea('Home Contact Card', $c, static function (): void { ?>
                    <p class="section-eyebrow">New or unsure?</p>
                    <h2>We can help you feel at ease.</h2>
                    <p>
                        If you are wondering about what to wear, worship style, children, accessibility, or what
                        happens when you arrive, please get in touch.
                    </p>
                    <p class="visit-side-card__detail">Millbrook Community Centre, Larne</p>
                    <a class="button button--secondary" href="/contact">Ask a question</a>
                <?php }); ?>
            </aside>
        </div>
    </section>
</main>

<?php $this->inc('elements/footer.php'); ?>
