// Google Translate: loads Google's widget into a hidden container and drives it from
// our own language menus ([data-lang] buttons). The choice persists across pages via
// Google's "googtrans" cookie, which the widget reads when it loads.
// Google's scripts are only downloaded when a visitor picks a language (or picked
// one earlier), so English visitors don't pay for them.

const mount = document.querySelector('[data-google-translate]');

const sourceLang = mount?.dataset.sourceLang || 'en';
const languages = (mount?.dataset.languages || '').split(',').filter(Boolean);

// Cookie is written on the bare host and the parent domain so it also holds on www./sub-domains.
const cookieDomains = () => {
    const host = window.location.hostname;
    const domains = [''];
    if (host.includes('.')) {
        domains.push(host);
        const parts = host.split('.');
        if (parts.length > 2) domains.push(`.${parts.slice(-2).join('.')}`);
    }
    return domains;
};

const writeCookie = (value, days) => {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    cookieDomains().forEach((domain) => {
        document.cookie = `googtrans=${value}; expires=${expires}; path=/${domain ? `; domain=${domain}` : ''}`;
    });
};

const currentLang = () => {
    const match = document.cookie.match(/(?:^|;\s*)googtrans=\/[^/;]*\/([^;]+)/);
    const lang = match ? decodeURIComponent(match[1]) : sourceLang;
    return languages.includes(lang) ? lang : sourceLang;
};

const label = (lang) => lang.split('-')[0].toUpperCase();

const syncMenus = (lang) => {
    document.querySelectorAll('[data-current-lang]').forEach((el) => { el.textContent = label(lang); });
    document.querySelectorAll('[data-lang]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(btn.dataset.lang === lang));
    });
    document.documentElement.lang = lang;
};

// Google's widget renders a hidden <select class="goog-te-combo">; changing it translates in place.
const selectInWidget = (lang, attempt = 0) => {
    const combo = document.querySelector('.goog-te-combo');
    if (!combo || combo.options.length === 0) {
        if (attempt < 40) window.setTimeout(() => selectInWidget(lang, attempt + 1), 150);
        else window.location.reload(); // widget never came up; the cookie applies the language on reload
        return;
    }
    combo.value = lang;
    combo.dispatchEvent(new Event('change'));
};

const setLanguage = (lang) => {
    if (!languages.includes(lang) || lang === currentLang()) return;

    if (lang === sourceLang) {
        // Restoring the original text is only reliable via a clean reload.
        writeCookie('', -1);
        window.location.reload();
        return;
    }

    writeCookie(`/${sourceLang}/${lang}`, 365);
    syncMenus(lang);
    loadWidget();
    selectInWidget(lang);
};

let widgetRequested = false;

const loadWidget = () => {
    if (widgetRequested) return;
    widgetRequested = true;

    const script = document.createElement('script');
    script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    script.async = true;
    document.head.appendChild(script);
};

if (mount) {
    syncMenus(currentLang());

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-lang]');
        if (!button) return;

        button.closest('details')?.removeAttribute('open');
        setLanguage(button.dataset.lang);
    });

    window.googleTranslateElementInit = () => {
        new window.google.translate.TranslateElement(
            {
                pageLanguage: sourceLang,
                includedLanguages: languages.join(','),
                autoDisplay: false,
            },
            mount.id,
        );
    };

    // A language chosen on an earlier page: translate this one too.
    if (currentLang() !== sourceLang) loadWidget();

    // Warm up when someone opens the language menu, so switching feels instant.
    document.querySelectorAll('[data-lang]').forEach((button) => {
        button.closest('details')?.addEventListener('toggle', loadWidget, { once: true });
    });
}
