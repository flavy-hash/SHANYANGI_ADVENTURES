const prefersReducedMotion = () => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

// Header turns from transparent to beige glass as soon as the page scrolls.
const initHeaderScroll = () => {
    const header = document.querySelector('[data-site-header]');
    if (!header) return;

    const update = () => header.classList.toggle('tm-header-scrolled', window.scrollY > 0);
    update();
    window.addEventListener('scroll', update, { passive: true });
};

// Off-canvas mobile drawer. Opened by the header hamburger or the bottom-nav "Menu" tab;
// focus returns to whichever button opened it.
const initMobileMenu = () => {
    const panel = document.querySelector('[data-mobile-panel]');
    const toggles = [...document.querySelectorAll('[data-mobile-toggle], [data-mobile-toggle-alias]')];
    if (!panel || toggles.length === 0) return;

    let opener = toggles[0];

    const setOpen = (open) => {
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        toggles.forEach((t) => t.setAttribute('aria-expanded', String(open)));
        document.body.classList.toggle('tm-mobile-nav-open', open);
        if (open) panel.querySelector('[data-mobile-close]')?.focus();
    };

    toggles.forEach((t) => t.addEventListener('click', () => {
        opener = t;
        setOpen(true);
    }));
    panel.querySelectorAll('[data-mobile-close]').forEach((el) => el.addEventListener('click', () => setOpen(false)));
    panel.addEventListener('click', (event) => {
        if (event.target === panel || event.target.closest('a, [data-lang]')) setOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && panel.classList.contains('is-open')) {
            setOpen(false);
            opener.focus();
        }
    });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) setOpen(false);
    });
};

// Navigation dropdowns. Each [data-menu-group] holds [data-menu-trigger] buttons whose
// aria-controls panel is shown/hidden; only one panel per group is open at a time.
// Groups marked [data-menu-hover] also open on hover (desktop mega menu) and close
// 150ms after the pointer leaves, so moving from trigger to panel doesn't flicker.
const initNavDropdowns = () => {
    const hoverQuery = window.matchMedia('(min-width: 1024px) and (hover: hover)');

    document.querySelectorAll('[data-menu-group]').forEach((group) => {
        const triggers = [...group.querySelectorAll('[data-menu-trigger]')];
        const hoverable = group.hasAttribute('data-menu-hover');
        let closeTimer;

        const set = (trigger, open) => {
            const panel = document.getElementById(trigger.getAttribute('aria-controls'));
            if (open && panel.hidden) panel.dispatchEvent(new CustomEvent('menu:open'));
            trigger.setAttribute('aria-expanded', String(open));
            panel.hidden = !open;
        };
        const closeAll = (except) => triggers.forEach((t) => t !== except && set(t, false));
        const open = (trigger) => {
            closeAll(trigger);
            set(trigger, true);
        };
        const isOpen = (trigger) => trigger.getAttribute('aria-expanded') === 'true';

        triggers.forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                // A mouse click on a hover-opened menu keeps it open; keyboard/touch toggles.
                if (hoverable && hoverQuery.matches && event.detail > 0) open(trigger);
                else if (isOpen(trigger)) set(trigger, false);
                else open(trigger);
            });

            if (!hoverable) return;
            const item = trigger.parentElement;
            item.addEventListener('mouseenter', () => {
                if (!hoverQuery.matches) return;
                clearTimeout(closeTimer);
                open(trigger);
            });
            item.addEventListener('mouseleave', () => {
                if (!hoverQuery.matches) return;
                closeTimer = setTimeout(() => closeAll(), 150);
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            const current = triggers.find(isOpen);
            if (!current) return;
            closeAll();
            if (group.contains(document.activeElement)) current.focus();
        });

        if (hoverable) {
            document.addEventListener('click', (event) => {
                if (!group.contains(event.target)) closeAll();
            });
            group.addEventListener('focusout', (event) => {
                if (!group.contains(event.relatedTarget)) closeAll();
            });
        }

        hoverQuery.addEventListener('change', () => closeAll());
    });
};

// Mega panel previews: hovering or focusing a link in the panel's list shows that
// link's title, text, CTA and image (its [data-mega-target] preview). Each time the
// panel opens it resets to the default preview and preloads the panel's images.
const initMegaPreviews = () => {
    document.querySelectorAll('[data-mega-panel]').forEach((panel) => {
        const previews = [...panel.querySelectorAll('[data-mega-preview]')];
        const links = [...panel.querySelectorAll('[data-mega-target]')];
        if (previews.length < 2) return;

        const show = (link) => {
            const target = link ? link.dataset.megaTarget : previews[0].id;
            links.forEach((l) => l.classList.toggle('is-active', l === link));
            previews.forEach((p) => {
                const visible = p.id === target;
                if (p.hidden === visible) p.hidden = !visible;
            });
        };

        panel.addEventListener('menu:open', () => {
            show(null);
            panel.querySelectorAll('img[loading="lazy"]').forEach((img) => { img.loading = 'eager'; });
        });

        links.forEach((link) => {
            link.addEventListener('mouseenter', () => show(link));
            link.addEventListener('focus', () => show(link));
        });
    });
};

// Native <dialog> popups: [data-dialog-open="id"] opens, [data-dialog-close] or a click
// on the backdrop closes. Escape is handled by the browser.
const initDialogs = () => {
    // Reopen a dialog after a form round-trip (validation errors or a success message).
    document.querySelectorAll('dialog[data-open-on-load]').forEach((dialog) => dialog.showModal());

    // Forms inside dialogs: block double submits while the server works.
    document.querySelectorAll('dialog form[method="POST"]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = 'Please wait...';
            }
        });
    });

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-dialog-open]');
        if (opener) {
            document.getElementById(opener.dataset.dialogOpen)?.showModal();
            return;
        }

        if (event.target.closest('[data-dialog-close]')) {
            event.target.closest('dialog')?.close();
            return;
        }

        // A click on the backdrop targets the <dialog> itself, outside its content box.
        if (event.target instanceof HTMLDialogElement && event.target.open) {
            const r = event.target.getBoundingClientRect();
            const inside = event.clientX >= r.left && event.clientX <= r.right && event.clientY >= r.top && event.clientY <= r.bottom;
            if (!inside) event.target.close();
        }
    });
};

// Newsletter form: submit in place and show the server's message. Without JS the
// form still posts normally and the message comes back via the session.
const initNewsletter = () => {
    document.querySelectorAll('[data-newsletter-form]').forEach((form) => {
        const status = form.parentElement.querySelector('[data-newsletter-status]');
        const button = form.querySelector('button[type="submit"]');
        const email = form.querySelector('input[type="email"]');

        const show = (message, ok) => {
            status.textContent = message;
            status.classList.toggle('is-success', ok);
            status.classList.toggle('is-error', !ok);
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!email.value.trim() || !email.checkValidity()) {
                show('Please enter a valid email address.', false);
                email.focus();
                return;
            }

            button.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: new FormData(form),
                });
                const data = await response.json().catch(() => ({}));

                if (response.ok) {
                    show(data.message || 'Thank you for subscribing!', true);
                    form.reset();
                } else if (response.status === 429) {
                    show('Too many attempts. Please try again in a minute.', false);
                } else if (response.status === 419) {
                    show('Your session expired. Please refresh the page and try again.', false);
                } else {
                    show(data.message || 'Something went wrong. Please try again.', false);
                }
            } catch {
                show('Could not connect. Please check your connection and try again.', false);
            } finally {
                button.disabled = false;
            }
        });
    });
};

// Close the language <details> dropdowns when clicking elsewhere.
const initDropdowns = () => {
    document.addEventListener('click', (event) => {
        document.querySelectorAll('details[data-dropdown][open]').forEach((details) => {
            if (!details.contains(event.target)) details.open = false;
        });
    });
};

// Hero headline: delete and retype each phrase in turn. Phrases are read from the
// [data-hero-phrase] elements on every step, so Google Translate output is picked up live.
const initTypewriter = () => {
    const el = document.querySelector('[data-hero-typewriter]');
    const sources = [...document.querySelectorAll('[data-hero-phrase]')];
    if (!el || sources.length === 0) return;

    const phrase = (i) => sources[i].textContent.replace(/\s+/g, ' ').trim().replace(/[.。]$/, '');

    el.textContent = phrase(0);

    if (sources.length === 1 || prefersReducedMotion()) {
        new MutationObserver(() => { el.textContent = phrase(0); })
            .observe(sources[0], { subtree: true, childList: true, characterData: true });
        return;
    }

    let index = 0;
    let deleting = true;

    const step = () => {
        const target = phrase(index);
        const current = el.textContent;

        if (deleting) {
            el.textContent = current.slice(0, -1);
            if (el.textContent.length === 0) {
                deleting = false;
                index = (index + 1) % sources.length;
                window.setTimeout(step, 520);
                return;
            }
        } else {
            el.textContent = target.slice(0, current.length + 1);
            if (el.textContent.length >= target.length) {
                deleting = true;
                window.setTimeout(step, 2600);
                return;
            }
        }

        window.setTimeout(step, deleting ? 75 : 105);
    };

    window.setTimeout(step, 2200);
};

// Hero media drifts down slower than the page scrolls.
const initHeroParallax = () => {
    const hero = document.querySelector('[data-hero-parallax]');
    if (!hero || prefersReducedMotion()) return;

    let queued = false;
    const update = () => {
        queued = false;
        const rect = hero.getBoundingClientRect();
        if (rect.bottom <= 0) return;

        const scrolled = Math.min(Math.max(-rect.top, 0), rect.height);
        const shift = Math.min(Math.min(180, rect.height * 0.26), scrolled * 0.38);
        hero.style.setProperty('--tm-hero-video-shift', `${shift.toFixed(1)}px`);
    };
    const queue = () => {
        if (queued) return;
        queued = true;
        window.requestAnimationFrame(update);
    };

    update();
    window.addEventListener('scroll', queue, { passive: true });
    window.addEventListener('resize', queue, { passive: true });
};

initHeaderScroll();
initMobileMenu();
initNavDropdowns();
initMegaPreviews();
initDialogs();
initNewsletter();
initDropdowns();
initTypewriter();
initHeroParallax();
