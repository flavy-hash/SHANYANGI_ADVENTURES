<?php

/*
|--------------------------------------------------------------------------
| Visitor activity log
|--------------------------------------------------------------------------
|
| Privacy-friendly page view tracking (App\Http\Middleware\LogPageView):
| - no cookies, no IP addresses, no full user agents are stored;
| - visitors get an anonymous ID that changes every day, so individuals
|   can't be followed across days;
| - the visitor's country is looked up on this server from a local database
|   (no outside service) and only the country code is kept;
| - browsers sending "Do Not Track" or "Global Privacy Control" are skipped;
| - logged-in staff and known bots are not counted;
| - records older than retention_days are deleted by `php artisan model:prune`
|   (scheduled daily in routes/console.php).
|
*/

return [

    'enabled' => (bool) env('ANALYTICS_ENABLED', true),

    'retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 180),

    /*
     | Country lookup (App\Support\GeoIp). Uses the free "IP to Country Lite"
     | database by DB-IP (https://db-ip.com, CC BY 4.0), downloaded with
     | `php artisan analytics:update-geoip` and refreshed monthly by the scheduler.
     | Behind Cloudflare, its CF-IPCountry header is used instead.
     */
    'geoip' => [
        'database' => env('GEOIP_DATABASE', storage_path('app/geoip/dbip-country-lite.mmdb')),
        'download_url' => 'https://download.db-ip.com/free/dbip-country-lite-{month}.mmdb.gz',
    ],

];
