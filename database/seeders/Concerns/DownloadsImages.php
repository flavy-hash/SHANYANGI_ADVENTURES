<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait DownloadsImages
{
    /**
     * Download a remote image into the media disk (once) and return its path.
     * Falls back to the original URL if downloading is disabled or fails.
     */
    protected function image(?string $url): ?string
    {
        if (! $url || ! str_starts_with($url, 'http') || ! config('media.seed_download_images')) {
            return $url;
        }

        $name = preg_match('/photo-[\w-]+/', $url, $m) ? $m[0] : md5($url);
        $path = "seed/{$name}.jpg";
        $disk = Storage::disk('media');

        if ($disk->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(30)->get(preg_replace('/([?&])w=\d+/', '$1w=1600', $url));

            if ($response->successful() && str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                $disk->put($path, $response->body());

                return $path;
            }
        } catch (Throwable) {
            // fall through to the URL
        }

        $this->command?->warn("Could not download {$url}; keeping the remote URL.");

        return $url;
    }
}
