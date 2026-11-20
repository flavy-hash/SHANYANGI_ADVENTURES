<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

/**
 * Home page hero (headlines, buttons, background photo and video), edited in the
 * admin under Pages → Home page.
 *
 * Background video, in order of preference:
 *  1. an uploaded video converted to an encrypted HLS stream (needs ffmpeg on the server),
 *  2. the uploaded video file itself, served through signed, session-bound links,
 *  3. the original stream from config/site.php (hero_stream).
 */
class HomePage
{
    public const KEY = 'home';

    /** Streams built from uploads are named "hero-…"; the original "hero" stream is never deleted. */
    public const STREAM_PREFIX = 'hero-';

    public const FALLBACK_POSTER = 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=2000&q=80';

    public static function defaults(): array
    {
        return [
            'hero_phrases' => ['Your Local Guide to Unforgettable Safaris', 'Welcome to Shanyangi Adventures'],
            'hero_subtitle' => null,
            'hero_cta_label' => 'Explore Experiences',
            'hero_cta_url' => '/safaris',
            'hero_cta2_label' => null,
            'hero_cta2_url' => null,
            'hero_poster' => null,
            'hero_video' => null,
            'hero_stream' => null,
            'hero_show_video' => true,
        ];
    }

    /** Saved content over the defaults. */
    public static function content(): array
    {
        $content = SiteSetting::get(self::KEY, self::defaults());
        $content['hero_phrases'] = array_values(array_filter(array_map('trim', (array) $content['hero_phrases']))) ?: self::defaults()['hero_phrases'];
        $content['hero_show_video'] = (bool) (SiteSetting::raw(self::KEY)['hero_show_video'] ?? true);

        return $content;
    }

    /**
     * Everything the hero needs to render.
     *
     * @return array{phrases: list<string>, subtitle: ?string, buttons: list<array{label: string, url: string}>, poster: string, video: ?array{type: string, url: string}}
     */
    public static function hero(): array
    {
        $c = self::content();

        // Longest phrase first: the headline never grows past its first size, which keeps the
        // page's "largest contentful paint" at first render instead of when a longer phrase is typed.
        $phrases = $c['hero_phrases'];
        usort($phrases, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        $buttons = [];
        foreach (['hero_cta', 'hero_cta2'] as $key) {
            if (filled($c[$key.'_label'] ?? null) && filled($c[$key.'_url'] ?? null)) {
                $buttons[] = ['label' => $c[$key.'_label'], 'url' => url($c[$key.'_url'])];
            }
        }

        return [
            'phrases' => $phrases,
            'subtitle' => $c['hero_subtitle'] ?: null,
            'buttons' => $buttons,
            'poster' => ProtectedMedia::src($c['hero_poster'] ?: null, 1600)
                ?? ProtectedMedia::imageUrl(config('site.hero_poster'))
                ?? self::FALLBACK_POSTER,
            'video' => $c['hero_show_video'] ? self::video($c) : null,
        ];
    }

    /** @return array{type: string, url: string}|null */
    protected static function video(array $c): ?array
    {
        if ($c['hero_stream'] && ($url = ProtectedMedia::streamUrl($c['hero_stream']))) {
            return ['type' => 'stream', 'url' => $url];
        }

        if ($c['hero_video'] && ($url = ProtectedMedia::imageUrl($c['hero_video'], null))) {
            return ['type' => 'file', 'url' => $url];
        }

        if ($c['hero_video']) {
            return null; // an uploaded video whose file is missing: show the photo, not the old video
        }

        return ($url = ProtectedMedia::streamUrl(config('site.hero_stream'))) ? ['type' => 'stream', 'url' => $url] : null;
    }

    public static function canConvertVideos(): bool
    {
        static $can = null;

        try {
            return $can ??= Process::timeout(10)->run([config('media.ffmpeg'), '-version'])->successful();
        } catch (Throwable) {
            return $can = false; // ffmpeg missing, or the host doesn't allow running programs
        }
    }

    /**
     * Turn an uploaded video (path on the media disk) into an encrypted stream.
     *
     * @return string|null the stream name, or null when the server can't convert videos
     */
    public static function convertVideo(string $path): ?string
    {
        if (! self::canConvertVideos()) {
            return null;
        }

        $name = self::STREAM_PREFIX.substr(sha1($path), 0, 10);
        @set_time_limit(900);

        try {
            $exit = Artisan::call('media:encrypt-video', [
                'source' => ProtectedMedia::root('images/'.$path),
                'name' => $name,
                '--crf' => 27,
                '--max-width' => 1280,
                '--segment' => 2,
            ]);
        } catch (Throwable) {
            return null;
        }

        return $exit === 0 && ProtectedMedia::streamUrl($name) ? $name : null;
    }

    /** Delete a stream built from an upload (never the original one). */
    public static function deleteStream(?string $name): void
    {
        if ($name && str_starts_with($name, self::STREAM_PREFIX) && ProtectedMedia::isValidName($name)) {
            File::deleteDirectory(ProtectedMedia::root("streams/{$name}"));
        }
    }
}
