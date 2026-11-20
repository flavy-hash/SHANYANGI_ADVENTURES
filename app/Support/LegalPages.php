<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Privacy policy, terms & conditions and refund policy pages.
 *
 * Text is Markdown: the default wording lives in resources/content/legal/*.md and can be
 * edited in the admin (Pages → Legal pages), which saves it in site settings. Words in
 * braces such as {email} are filled in from config/site.php when the page is shown.
 */
class LegalPages
{
    public const KEY = 'legal';

    public const PAGES = [
        'privacy' => ['title' => 'Privacy Policy', 'route' => 'legal.privacy', 'intro' => 'How we collect, use and protect your personal information.'],
        'terms' => ['title' => 'Terms & Conditions', 'route' => 'legal.terms', 'intro' => 'Payment terms and conditions for safari bookings, tours, excursions and related travel services.'],
        'refund' => ['title' => 'Refund Policy', 'route' => 'legal.refund', 'intro' => 'What happens if you cancel or change your trip, or if we have to.'],
    ];

    /** Date shown as "Last updated" until a page is edited in the admin. */
    public const DEFAULT_UPDATED = '2026-10-12';

    /** Pages whose default text changed later than DEFAULT_UPDATED. */
    public const UPDATED = ['terms' => '2026-10-17', 'refund' => '2026-10-17'];

    public static function defaultBody(string $page): string
    {
        $file = resource_path("content/legal/{$page}.md");

        return is_file($file) ? (string) file_get_contents($file) : '';
    }

    /** Markdown for a page: the admin's version if saved, otherwise the default text. */
    public static function body(string $page): string
    {
        $saved = trim((string) (SiteSetting::raw(self::KEY)[$page]['body'] ?? ''));

        return $saved !== '' ? $saved : self::defaultBody($page);
    }

    public static function updatedOn(string $page): Carbon
    {
        $date = SiteSetting::raw(self::KEY)[$page]['updated_on'] ?? null;

        return Carbon::parse($date ?: (self::UPDATED[$page] ?? self::DEFAULT_UPDATED));
    }

    /**
     * Rendered page: HTML (raw HTML in the Markdown is stripped) plus a table of contents
     * built from the "## " headings.
     *
     * @return array{html: HtmlString, toc: list<array{id: string, title: string}>}
     */
    public static function render(string $page): array
    {
        $html = Str::markdown(self::fillPlaceholders(self::body($page)), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $toc = [];
        $html = preg_replace_callback('#<h2>(.*?)</h2>#s', function (array $m) use (&$toc) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $id = Str::slug($title) ?: 'section-'.(count($toc) + 1);
            $toc[] = ['id' => $id, 'title' => $title];

            return '<h2 id="'.e($id).'">'.$m[1].'</h2>';
        }, $html);

        // Wide tables (e.g. cancellation charges) scroll on small screens instead of overflowing.
        $html = str_replace(['<table>', '</table>'], ['<div class="tm-legal-table"><table>', '</table></div>'], $html);

        return ['html' => new HtmlString($html), 'toc' => $toc];
    }

    public static function fillPlaceholders(string $markdown): string
    {
        $site = config('site');
        $contactUrl = route('contact');

        return strtr($markdown, [
            '{company}' => 'Shanyangi Adventures',
            '{email}' => filled($site['email'] ?? null) ? $site['email'] : 'our contact page',
            '{phone}' => filled($site['phone'] ?? null) ? $site['phone'] : (filled($site['whatsapp'] ?? null) ? '+'.preg_replace('/\D/', '', $site['whatsapp']) : 'the number on our contact page'),
            '{address}' => (string) ($site['address'] ?? ''),
            '{website}' => preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')),
            '{analytics_days}' => (string) config('analytics.retention_days'),
            '{contact_link}' => "[our contact page]({$contactUrl})",
            '{privacy_url}' => route('legal.privacy'),
            '{terms_url}' => route('legal.terms'),
            '{refund_url}' => route('legal.refund'),
        ]);
    }
}
