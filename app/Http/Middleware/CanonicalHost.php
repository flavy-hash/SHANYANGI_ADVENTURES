<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends visitors and search engines from any other host name (www / non-www twin, an old
 * domain pointing at this folder) to the same page on APP_URL's host with a permanent 301,
 * so Google lists the site once. Only GET/HEAD: form posts are never redirected.
 *
 * Off unless CANONICAL_REDIRECT=true, and skipped while APP_URL is a local address, so a
 * forgotten APP_URL=http://localhost can't send the live site to localhost.
 */
class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('seo.canonical_redirect') || ! $request->isMethodCacheable()) {
            return $next($request);
        }

        $target = parse_url((string) config('app.url'));
        $host = strtolower($target['host'] ?? '');

        if (! $this->isPublicHost($host) || strtolower($request->getHost()) === $host) {
            return $next($request);
        }

        $url = ($target['scheme'] ?? 'https').'://'.$host
            .(isset($target['port']) ? ':'.$target['port'] : '')
            .$request->getRequestUri();

        return redirect()->away($url, 301);
    }

    protected function isPublicHost(string $host): bool
    {
        return str_contains($host, '.')
            && $host !== 'localhost'
            && filter_var($host, FILTER_VALIDATE_IP) === false;
    }
}
