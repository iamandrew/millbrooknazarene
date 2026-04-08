document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.site-header');
    var toggle = document.querySelector('.mobile-nav-toggle');
    var mobileNav = document.getElementById('mobileNav');
    var mobileLinks = mobileNav ? mobileNav.querySelectorAll('a') : [];

    var closeMobileNav = function () {
        if (!toggle || !mobileNav) {
            return;
        }

        toggle.setAttribute('aria-expanded', 'false');
        mobileNav.classList.remove('open');
        document.body.classList.remove('nav-open');
    };

    if (toggle && mobileNav) {
        toggle.addEventListener('click', function () {
            var isOpen = mobileNav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('nav-open', isOpen);
        });

        mobileLinks.forEach(function (link) {
            link.addEventListener('click', closeMobileNav);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMobileNav();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                closeMobileNav();
            }
        });
    }

    if (header) {
        var syncScrollState = function () {
            header.classList.toggle('scrolled', window.scrollY > 24);
        };

        syncScrollState();
        window.addEventListener('scroll', syncScrollState, { passive: true });
    }
});
