<?php

/*
|--------------------------------------------------------------------------
| Protected Media
|--------------------------------------------------------------------------
|
| Site images and videos live outside /public and are only reachable through
| short-lived signed URLs bound to the visitor's session (see
| App\Support\ProtectedMedia). Videos are served as AES-128 encrypted HLS
| streams created with `php artisan media:encrypt-video`.
|
|   root/images/...           images, served via ProtectedMedia::imageUrl()
|   root/streams/{name}/...   encrypted HLS output (playlist, segments, key)
|   root/source/...           original uploads (never served)
|
*/

return [

    'root' => env('MEDIA_ROOT', storage_path('app/private/media')),

    // Minimum lifetime of a signed media URL. URLs are rounded up to the next
    // hour so they stay identical (and browser-cacheable) within that hour.
    'url_ttl_minutes' => (int) env('MEDIA_URL_TTL', 60),

    // ffmpeg binary used by media:encrypt-video.
    'ffmpeg' => env('FFMPEG_BINARY', 'ffmpeg'),

    // ContentSeeder downloads starter images into the media disk; disable to
    // keep remote URLs instead (the test suite does this).
    'seed_download_images' => (bool) env('SEED_DOWNLOAD_IMAGES', true),

];
