<?php

/*
|--------------------------------------------------------------------------
| Search engines (App\Support\Seo)
|--------------------------------------------------------------------------
|
| Canonical links, Open Graph tags, JSON-LD, /sitemap.xml and /robots.txt are all built
| from APP_URL, so the site is listed once under one address. Set APP_URL to the exact
| main address, e.g. https://www.shanyangiadventures.co.tz (no trailing slash).
|
*/

return [

    // 301-redirect GET/HEAD requests on any other host (www / non-www twin, old domains
    // pointing at this folder) to the same path on APP_URL's host. Turn on only once
    // APP_URL is the real live address.
    'canonical_redirect' => (bool) env('CANONICAL_REDIRECT', false),

    // Only the content="…" value from Google Search Console's / Bing's HTML tag method.
    'google_verification' => env('GOOGLE_SITE_VERIFICATION'),

    'bing_verification' => env('BING_SITE_VERIFICATION'),

    // Shared link preview (Facebook, WhatsApp, X…) when a page has no public photo of its own.
    // Must be a public file: protected media links only work inside a visitor's session.
    'default_image' => 'images/shanyangi.jpeg',

    // Business logo in structured data (square or wide, public file).
    'logo' => 'images/logo/shanyangi-logo.png',

    // ISO code for prices in structured data (config('packages.currency') is the symbol shown).
    'currency' => env('SEO_CURRENCY', 'USD'),

    // Paths kept out of search engines (robots.txt).
    'disallow' => ['/admin', '/livewire', '/media/', '/quotes/', '/chat/', '/up'],

];
