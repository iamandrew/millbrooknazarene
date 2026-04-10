document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.site-header');
    var toggle = document.querySelector('.menu-toggle');
    var siteMenu = document.getElementById('siteMenu');
    var closeButtons = siteMenu ? siteMenu.querySelectorAll('[data-menu-close]') : [];
    var menuLinks = siteMenu ? siteMenu.querySelectorAll('a') : [];

    var closeMenu = function () {
        if (!toggle || !siteMenu) {
            return;
        }

        toggle.setAttribute('aria-expanded', 'false');
        siteMenu.classList.remove('open');
        siteMenu.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('menu-open');
    };

    if (toggle && siteMenu) {
        toggle.addEventListener('click', function () {
            var isOpen = siteMenu.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            siteMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.classList.toggle('menu-open', isOpen);
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeMenu);
        });

        menuLinks.forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
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
