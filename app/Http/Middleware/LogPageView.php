<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Records anonymous page views for the admin's Visitor activity log.
 *
 * Privacy by design (see config/analytics.php):
 * - no cookies; no IP address or full user agent is stored;
 * - the visitor ID is a hash of IP + browser + today's date + the app key,
 *   so it changes every day and can't be reversed or used across days;
 * - "Do Not Track" / "Global Privacy Control" browsers, logged-in staff,
 *   bots and link prefetches are skipped.
 *
 * Logging happens in terminate(), after the response has been sent.
 */
class LogPageView
{
    /**
     * Route name => page type shown in the admin.
     */
    protected const PAGE_TYPES = [
        'home' => 'home',
        'safaris' => 'safaris',
        'safaris.show' => 'package',
        'activities' => 'activities',
        'accommodations' => 'accommodations',
        'blog' => 'blog',
        'blog.category' => 'blog',
        'blog.show' => 'blog-post',
        'about' => 'about',
        'reviews' => 'reviews',
        'contact' => 'contact',
    ];

    protected const BOT_PATTERN = '/bot|crawl|spider|slurp|preview|facebookexternalhit|embedly|headless|lighthouse|monitor|curl|wget|python|httpclient|axios/i';

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldLog($request, $response)) {
            return;
        }

        try {
            $userAgent = (string) $request->userAgent();

            PageView::create([
                'visitor_hash' => substr(hash_hmac('sha256', $request->ip().'|'.$userAgent.'|'.now()->toDateString(), (string) config('app.key')), 0, 16),
                'path' => mb_substr('/'.ltrim($request->path(), '/'), 0, 255),
                'page_type' => self::PAGE_TYPES[$request->route()?->getName()] ?? 'other',
                'package_id' => $request->attributes->get('viewed_package_id'),
                'device' => $this->device($userAgent),
                'referrer_host' => $this->referrerHost($request),
            ]);
        } catch (Throwable $e) {
            // Analytics must never break the website.
            Log::warning('Page view not recorded: '.$e->getMessage());
        }
    }

    protected function shouldLog(Request $request, Response $response): bool
    {
        $userAgent = (string) $request->userAgent();

        return config('analytics.enabled')
            && $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && ! $request->is('admin', 'admin/*', 'livewire*', 'media/*', 'chat/*', 'quotes/*')
            && $request->route() !== null
            && ! $request->user()
            && $request->header('DNT') !== '1'
            && $request->header('Sec-GPC') !== '1'
            && ! in_array(strtolower((string) ($request->header('Sec-Purpose') ?? $request->header('Purpose'))), ['prefetch', 'prerender'], true)
            && $userAgent !== ''
            && ! preg_match(self::BOT_PATTERN, $userAgent);
    }

    protected function device(string $userAgent): string
    {
        return match (true) {
            (bool) preg_match('/ipad|tablet|kindle|silk|playbook|android(?!.*mobile)/i', $userAgent) => 'tablet',
            (bool) preg_match('/mobi|iphone|ipod|android|blackberry|opera mini|iemobile/i', $userAgent) => 'mobile',
            default => 'desktop',
        };
    }

    /**
     * Host of the referring site; internal navigation counts as no referrer.
     */
    protected function referrerHost(Request $request): ?string
    {
        $host = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        if (! $host || strcasecmp($host, $request->getHost()) === 0) {
            return null;
        }

        return mb_substr(preg_replace('/^www\./i', '', strtolower($host)), 0, 120);
    }
}
