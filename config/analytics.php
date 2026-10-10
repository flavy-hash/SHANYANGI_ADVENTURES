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
| - browsers sending "Do Not Track" or "Global Privacy Control" are skipped;
| - logged-in staff and known bots are not counted;
| - records older than retention_days are deleted by `php artisan model:prune`
|   (scheduled daily in routes/console.php).
|
*/

return [

    'enabled' => (bool) env('ANALYTICS_ENABLED', true),

    'retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 180),

];
