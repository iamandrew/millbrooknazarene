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

    document.querySelectorAll('[data-giving-widget]').forEach(function (widget) {
        var form = widget.querySelector('[data-giving-form]');
        var amountsContainer = widget.querySelector('[data-giving-amounts]');
        var customAmount = widget.querySelector('[data-giving-custom-amount]');
        var submitButton = widget.querySelector('[data-giving-submit]');
        var status = widget.querySelector('[data-giving-status]');
        var campaignName = widget.querySelector('[data-giving-campaign-name]');
        var thanks = widget.querySelector('[data-giving-thanks]');
        var campaignId = widget.getAttribute('data-campaign-id') || '';
        var apiBase = (widget.getAttribute('data-api-base') || '').replace(/\/$/, '');
        var websiteBase = (widget.getAttribute('data-website-base') || '').replace(/\/$/, '');
        var checkoutPrefix = widget.getAttribute('data-checkout-prefix') || '/c/';
        var returnPath = widget.getAttribute('data-return-path') || window.location.pathname;
        var tag = (widget.getAttribute('data-tag') || '').slice(0, 36);
        var donationLimit = null;
        var selectedAmount = null;
        var checkoutCampaignId = campaignId;

        if (thanks && window.location.search.indexOf('thanks=1') !== -1) {
            thanks.hidden = false;
        }

        var setStatus = function (message, type) {
            if (!status) {
                return;
            }

            status.textContent = message;
            status.setAttribute('data-status', type || 'neutral');
        };

        var setInteractive = function (enabled) {
            widget.querySelectorAll('button, input').forEach(function (field) {
                field.disabled = !enabled;
            });
        };

        var formatCurrency = function (amount) {
            var formatter = new Intl.NumberFormat('en-GB', {
                style: 'currency',
                currency: 'GBP',
                minimumFractionDigits: Number.isInteger(amount) ? 0 : 2,
                maximumFractionDigits: 2
            });

            return formatter.format(amount);
        };

        var selectAmount = function (amount, activeButton) {
            selectedAmount = amount;

            if (customAmount && activeButton) {
                customAmount.value = '';
            }

            if (!amountsContainer) {
                return;
            }

            amountsContainer.querySelectorAll('[data-amount]').forEach(function (button) {
                var isSelected = button === activeButton;
                button.classList.toggle('is-selected', isSelected);
                button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
        };

        var wireAmountButtons = function () {
            if (!amountsContainer) {
                return;
            }

            amountsContainer.querySelectorAll('[data-amount]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var amount = parseFloat(button.getAttribute('data-amount'));

                    if (!Number.isNaN(amount)) {
                        selectAmount(amount, button);
                    }
                });
            });
        };

        var renderAmounts = function (items) {
            if (!amountsContainer || !items || !items.length) {
                wireAmountButtons();
                return;
            }

            amountsContainer.innerHTML = '';

            items.forEach(function (item) {
                var amount = parseFloat(item.amount);

                if (Number.isNaN(amount)) {
                    return;
                }

                var button = document.createElement('button');
                button.className = 'giving-amount-option';
                button.type = 'button';
                button.setAttribute('data-amount', String(amount));
                button.setAttribute('aria-pressed', 'false');
                button.textContent = formatCurrency(amount);
                amountsContainer.appendChild(button);
            });

            wireAmountButtons();

            var defaultItem = items.find(function (item) {
                return item.default;
            }) || items[0];
            var defaultAmount = defaultItem ? parseFloat(defaultItem.amount) : null;
            var defaultButton = defaultAmount !== null ? amountsContainer.querySelector('[data-amount="' + defaultAmount + '"]') : null;

            if (!Number.isNaN(defaultAmount) && defaultButton) {
                selectAmount(defaultAmount, defaultButton);
            }
        };

        var getReturnUrl = function () {
            return new URL(returnPath, window.location.origin).toString();
        };

        var disableWithMessage = function (message) {
            setInteractive(false);
            setStatus(message, 'error');
            widget.classList.add('is-unavailable');
        };

        if (!form || !amountsContainer || !submitButton) {
            return;
        }

        setInteractive(false);
        wireAmountButtons();

        if (customAmount) {
            customAmount.addEventListener('input', function () {
                selectedAmount = null;

                if (amountsContainer) {
                    amountsContainer.querySelectorAll('[data-amount]').forEach(function (button) {
                        button.classList.remove('is-selected');
                        button.setAttribute('aria-pressed', 'false');
                    });
                }
            });
        }

        if (!campaignId || !apiBase || !websiteBase || typeof fetch === 'undefined') {
            disableWithMessage('The Give A Little test campaign is not configured yet.');
            return;
        }

        fetch(apiBase + '/webdonations/campaigns/' + encodeURIComponent(campaignId), {
            headers: {
                Accept: 'application/json'
            }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Campaign unavailable');
            }

            return response.json();
        }).then(function (campaign) {
            checkoutCampaignId = campaign.id || campaignId;
            donationLimit = campaign.donationLimit ? parseFloat(campaign.donationLimit) : null;

            if (campaignName && (campaign.charityName || campaign.heading)) {
                campaignName.textContent = campaign.charityName || campaign.heading;
            }

            renderAmounts(campaign.items || []);

            if (customAmount && campaign.showCustomAmount === false) {
                customAmount.closest('.giving-custom-amount').hidden = true;
            }

            if (campaign.allowRecurring === false) {
                widget.querySelectorAll('input[name="giving_frequency"][value="monthly"]').forEach(function (input) {
                    input.closest('label').hidden = true;
                    input.checked = false;
                });
            }

            setInteractive(true);
            setStatus('Ready to continue to Give A Little’s secure test checkout.', 'success');
        }).catch(function () {
            disableWithMessage('The Give A Little test campaign could not be found. Add the published test campaign ID to enable this form.');
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var customValue = customAmount ? parseFloat(customAmount.value) : NaN;
            var amount = !Number.isNaN(customValue) && customValue > 0 ? customValue : selectedAmount;

            if (!amount || amount <= 0) {
                setStatus('Please choose or enter an amount before continuing.', 'error');
                return;
            }

            if (donationLimit && amount > donationLimit) {
                setStatus('The maximum online gift for this campaign is ' + formatCurrency(donationLimit) + '.', 'error');
                return;
            }

            var frequency = form.querySelector('input[name="giving_frequency"]:checked');
            var isRecurring = frequency && frequency.value === 'monthly';
            var checkoutPath = checkoutPrefix + encodeURIComponent(checkoutCampaignId) + '/initiate-donation';
            var checkoutUrl = new URL(checkoutPath, websiteBase + '/');

            checkoutUrl.searchParams.set('amount', amount.toFixed(2));
            checkoutUrl.searchParams.set('recurring', isRecurring ? 'true' : 'false');
            checkoutUrl.searchParams.set('returnUrl', getReturnUrl());

            if (tag) {
                checkoutUrl.searchParams.set('tag', tag);
            }

            window.location.href = checkoutUrl.toString();
        });
    });

    document.querySelectorAll('[data-kids-club-form]').forEach(function (form) {
        var dobInput = form.querySelector('[data-kids-club-dob]');
        var ageOutput = form.querySelector('[data-kids-club-age-output]');
        var ageInput = form.querySelector('[data-kids-club-age-input]');
        var submittedAtInput = form.querySelector('[data-kids-club-submitted-at]');
        var status = form.querySelector('[data-kids-club-status]');
        var firstClubDate = new Date('2026-08-12T12:00:00');

        var setStatus = function (message, type) {
            if (!status) {
                return;
            }

            status.textContent = message;
            status.setAttribute('data-status', type || 'neutral');
        };

        var setInteractive = function (enabled) {
            form.querySelectorAll('button, input, textarea, select').forEach(function (field) {
                field.disabled = !enabled;
            });
        };

        var syncAge = function () {
            if (!dobInput || !ageOutput || !ageInput) {
                return;
            }

            if (!dobInput.value) {
                ageOutput.textContent = 'Age will appear here once a date is selected.';
                ageInput.value = '';
                return;
            }

            var dob = new Date(dobInput.value + 'T12:00:00');

            if (Number.isNaN(dob.getTime())) {
                ageOutput.textContent = 'Please check the date of birth.';
                ageInput.value = '';
                return;
            }

            var age = firstClubDate.getFullYear() - dob.getFullYear();
            var hasHadBirthday = firstClubDate.getMonth() > dob.getMonth()
                || (firstClubDate.getMonth() === dob.getMonth() && firstClubDate.getDate() >= dob.getDate());

            if (!hasHadBirthday) {
                age -= 1;
            }

            if (age < 0) {
                ageOutput.textContent = 'Please check the date of birth.';
                ageInput.value = '';
                return;
            }

            ageOutput.textContent = 'Age on 12 August 2026: ' + age;
            ageInput.value = String(age);
        };

        if (dobInput) {
            dobInput.addEventListener('change', syncAge);
            dobInput.addEventListener('input', syncAge);
            syncAge();
        }

        form.addEventListener('submit', function (event) {
            var endpoint = (form.getAttribute('data-google-sheet-endpoint') || form.getAttribute('action') || '').trim();
            var honeypot = form.querySelector('input[name="website"]');
            var data;

            event.preventDefault();

            if (honeypot && honeypot.value) {
                form.reset();
                setStatus('Thank you. The registration has been received.', 'success');
                return;
            }

            syncAge();

            if (!form.checkValidity()) {
                form.reportValidity();
                setStatus('Please complete the required details before sending.', 'error');
                return;
            }

            if (!endpoint) {
                setStatus('This form is ready, but the Google Sheet connection has not been added yet.', 'error');
                return;
            }

            if (typeof fetch === 'undefined') {
                setStatus('This browser cannot send the form. Please email info@millbrooknazarene.co.uk.', 'error');
                return;
            }

            if (submittedAtInput) {
                submittedAtInput.value = new Date().toISOString();
            }

            data = new FormData(form);
            setInteractive(false);
            setStatus('Sending registration...', 'neutral');

            fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json'
                },
                body: data
            }).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok || payload.ok === false) {
                        throw new Error(payload.message || 'The registration could not be sent.');
                    }

                    return payload;
                });
            }).then(function (payload) {
                form.reset();
                syncAge();
                setInteractive(true);
                setStatus(payload.message || 'Thank you. The registration has been sent to the Kids Club team.', 'success');
            }).catch(function (error) {
                setInteractive(true);
                setStatus(error.message || 'Sorry, the form could not be sent. Please try again or email info@millbrooknazarene.co.uk.', 'error');
            });
        });
    });
});
