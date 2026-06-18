document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.site-header');
    var toggle = document.querySelector('.menu-toggle');
    var siteMenu = document.getElementById('siteMenu');
    var closeButtons = siteMenu ? siteMenu.querySelectorAll('[data-menu-close]') : [];
    var menuLinks = siteMenu ? siteMenu.querySelectorAll('a') : [];
    var sermonPlayers = {};

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

    if (typeof Plyr !== 'undefined') {
        var playerNodes = document.querySelectorAll('.js-sermon-player');

        playerNodes.forEach(function (node) {
            var player = new Plyr(node, {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'settings'],
                settings: ['speed'],
                speed: { selected: 1, options: [0.75, 1, 1.25, 1.5] },
                iconUrl: '/application/themes/millbrook/vendor/plyr/plyr.svg'
            });

            if (node.id) {
                sermonPlayers[node.id] = player;
            }
        });
    }

    document.querySelectorAll('[data-sermon-play]').forEach(function (button) {
        button.addEventListener('click', function () {
            var playerId = button.getAttribute('data-sermon-play');
            var playerNode = playerId ? document.getElementById(playerId) : null;
            var player = playerId ? sermonPlayers[playerId] : null;

            if (!playerNode) {
                return;
            }

            playerNode.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (player && typeof player.play === 'function') {
                var playResult = player.play();
                if (playResult && typeof playResult.catch === 'function') {
                    playResult.catch(function () {
                        playerNode.focus();
                    });
                }
                return;
            }

            var fallbackResult = playerNode.play();
            if (fallbackResult && typeof fallbackResult.catch === 'function') {
                fallbackResult.catch(function () {
                    playerNode.focus();
                });
            }
        });
    });

    document.querySelectorAll('[data-sermon-load]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            var playerId = link.getAttribute('data-sermon-load');
            var playerNode = playerId ? document.getElementById(playerId) : null;
            var player = playerId ? sermonPlayers[playerId] : null;
            var featuredContainer = document.querySelector('[data-featured-sermon]');
            var streamUrl = link.getAttribute('data-sermon-stream');
            var title = link.getAttribute('data-sermon-title') || '';
            var meta = link.getAttribute('data-sermon-meta') || '';
            var description = link.getAttribute('data-sermon-description') || '';
            var descriptionHtml = link.getAttribute('data-sermon-description-html') || '';
            var imageUrl = link.getAttribute('data-sermon-image') || '';
            var sermonId = link.getAttribute('data-sermon-id') || '';
            var titleTargetId = link.getAttribute('data-sermon-title-target');
            var metaTargetId = link.getAttribute('data-sermon-meta-target');
            var descriptionTargetId = link.getAttribute('data-sermon-description-target');
            var imageTargetId = link.getAttribute('data-sermon-image-target');
            var eyebrowTargetId = link.getAttribute('data-sermon-eyebrow-target');
            var titleTarget = titleTargetId ? document.getElementById(titleTargetId) : null;
            var metaTarget = metaTargetId ? document.getElementById(metaTargetId) : null;
            var descriptionTarget = descriptionTargetId ? document.getElementById(descriptionTargetId) : null;
            var imageTarget = imageTargetId ? document.getElementById(imageTargetId) : null;
            var eyebrowTarget = eyebrowTargetId ? document.getElementById(eyebrowTargetId) : null;

            if (!playerNode || !streamUrl) {
                return;
            }

            event.preventDefault();

            if (titleTarget) {
                titleTarget.textContent = title;
            }

            if (metaTarget) {
                metaTarget.textContent = meta;
            }

            if (descriptionTarget) {
                if (descriptionHtml) {
                    descriptionTarget.innerHTML = descriptionHtml;
                } else {
                    descriptionTarget.textContent = description;
                }
                descriptionTarget.hidden = descriptionHtml === '' && description === '';
            }

            if (imageTarget) {
                var imageNode = imageTarget.querySelector('[data-sermon-image-img]');
                if (imageNode && imageUrl) {
                    imageNode.setAttribute('src', imageUrl);
                    imageNode.setAttribute('alt', 'Artwork for ' + title);
                    imageTarget.hidden = false;
                } else if (imageNode) {
                    imageNode.removeAttribute('src');
                    imageNode.setAttribute('alt', '');
                    imageTarget.hidden = true;
                }
            }

            if (eyebrowTarget) {
                eyebrowTarget.textContent = 'Now playing';
            }

            if (featuredContainer) {
                featuredContainer.setAttribute('data-sermon-id', sermonId);
                featuredContainer.setAttribute('data-sermon-title', title);
                featuredContainer.setAttribute('data-sermon-meta', meta);
                featuredContainer.setAttribute('data-sermon-description', description);
                featuredContainer.setAttribute('data-sermon-description-html', descriptionHtml);
                featuredContainer.setAttribute('data-sermon-stream', streamUrl);
                featuredContainer.setAttribute('data-sermon-image', imageUrl);
            }

            document.querySelectorAll('[data-sermon-item]').forEach(function (item) {
                item.classList.toggle('is-active', item.getAttribute('data-sermon-id') === sermonId);
            });

            if (player && player.media) {
                player.pause();
                player.media.setAttribute('src', streamUrl);
                player.media.load();
            } else {
                playerNode.pause();
                playerNode.setAttribute('src', streamUrl);
                playerNode.load();
            }

            playerNode.scrollIntoView({ behavior: 'smooth', block: 'center' });

            var playTarget = player && typeof player.play === 'function' ? player : playerNode;
            var playResult = playTarget.play();
            if (playResult && typeof playResult.catch === 'function') {
                playResult.catch(function () {
                    playerNode.focus();
                });
            }
        });
    });
});
