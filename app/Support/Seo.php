<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Package;

/**
 * Search engine helpers: page titles, canonical URLs, link-preview images and JSON-LD.
 *
 * Every URL handed to search engines (canonical, og:url, sitemap, JSON-LD) is built from
 * config('app.url') rather than the request host, so the site is listed once, under APP_URL,
 * even when it is reached on www / non-www or an old domain. Query strings are dropped:
 * /safaris?type=honeymoon is the same page as /safaris for search purposes.
 */
class Seo
{
    public static function brand(): string
    {
        return (string) config('site.name', 'Shanyangi Adventures');
    }

    /** "Page — Brand"; a title that already names the brand (the home page) is kept as it is. */
    public static function title(?string $page): string
    {
        $page = self::clean($page);

        if ($page === '') {
            return self::brand();
        }

        return str_contains(mb_strtolower($page), mb_strtolower(self::brand())) ? $page : $page.' — '.self::brand();
    }

    /** Absolute URL on APP_URL for a path or any URL (its host and query string are dropped). */
    public static function url(?string $pathOrUrl = '/'): string
    {
        $path = (string) parse_url((string) $pathOrUrl, PHP_URL_PATH);
        $path = trim($path, '/');

        return self::base().'/'.$path;
    }

    /** Canonical URL of the current page. */
    public static function canonical(): string
    {
        return self::url(request()->getPathInfo());
    }

    /** APP_URL without a trailing slash. */
    public static function base(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    /**
     * Public, absolute image for link previews and structured data. Protected media files only
     * open inside a visitor's session (crawlers can't load them), so those fall back to the
     * default share image; full http(s) URLs are used as they are.
     */
    public static function image(?string $image = null): string
    {
        return preg_match('#^https?://#i', (string) $image) ? $image : self::url(config('seo.default_image'));
    }

    /** Plain one-line text for meta tags (Blade sections arrive HTML-escaped). */
    public static function clean(?string $text, ?int $limit = null): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return $limit ? \Illuminate\Support\Str::limit($text, $limit, '…') : $text;
    }

    /** JSON for a <script type="application/ld+json"> block ("<" escaped so text can't close the tag). */
    public static function json(array $data): string
    {
        return json_encode(
            ['@context' => 'https://schema.org'] + $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_PRETTY_PRINT,
        );
    }

    /** The business and the website, on every page. */
    public static function organization(): array
    {
        $address = config('site.address');

        $business = array_filter([
            '@type' => 'TravelAgency',
            '@id' => self::url('/').'#organization',
            'name' => self::brand(),
            'url' => self::url('/'),
            'logo' => self::url(config('seo.logo')),
            'image' => self::url(config('seo.default_image')),
            'description' => 'Locally owned Tanzanian safaris, Kilimanjaro climbs and Zanzibar escapes, planned around you.',
            'telephone' => config('site.phone') ?: (config('site.whatsapp') ? '+'.preg_replace('/\D/', '', config('site.whatsapp')) : null),
            'email' => config('site.email'),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $address ?: null,
                'addressCountry' => 'TZ',
            ]),
            'areaServed' => ['@type' => 'Country', 'name' => 'Tanzania'],
            'sameAs' => array_values(array_column(SocialLinks::active(), 'url')) ?: null,
        ]);

        $website = [
            '@type' => 'WebSite',
            '@id' => self::url('/').'#website',
            'name' => self::brand(),
            'url' => self::url('/'),
            'publisher' => ['@id' => self::url('/').'#organization'],
        ];

        return ['@graph' => [$business, $website]];
    }

    /** A tour or safari package page. */
    public static function trip(Package $package): array
    {
        $url = self::url(route('safaris.show', $package->slug, false));
        $days = collect($package->itinerary ?? [])->values();

        $data = [
            '@type' => 'TouristTrip',
            '@id' => $url.'#trip',
            'name' => self::clean($package->title),
            'description' => self::clean($package->summary ?: $package->overview, 300),
            'url' => $url,
            'image' => self::image($package->image),
            'provider' => ['@id' => self::url('/').'#organization'],
        ];

        if ($package->days > 0 || $days->isNotEmpty()) {
            // Trip length as the day-by-day plan (schema.org has no "duration" for trips).
            $data['itinerary'] = [
                '@type' => 'ItemList',
                'numberOfItems' => max((int) $package->days, $days->count()),
                'itemListElement' => $days->map(fn ($day, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => self::clean('Day '.($i + 1).(filled($day['title'] ?? null) ? ': '.$day['title'] : '')),
                ])->all(),
            ];
        }

        if (($price = (float) $package->price) > 0) {
            $data['offers'] = [
                '@type' => 'Offer',
                'name' => 'From price per person',
                'price' => round($price, 2),
                'priceCurrency' => config('seo.currency'),
                'url' => $url,
                'availability' => 'https://schema.org/InStock',
            ];
        }

        return $data;
    }

    /** A blog article. */
    public static function article(BlogPost $post): array
    {
        $url = self::url(route('blog.show', $post->slug, false));

        return array_filter([
            '@type' => 'BlogPosting',
            '@id' => $url.'#article',
            'headline' => self::clean($post->title, 110),
            'description' => self::clean($post->excerpt, 300),
            'url' => $url,
            'mainEntityOfPage' => $url,
            'image' => self::image($post->image),
            'datePublished' => $post->published_at?->toDateString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => ['@id' => self::url('/').'#organization'],
            'publisher' => ['@id' => self::url('/').'#organization'],
        ]);
    }
}
