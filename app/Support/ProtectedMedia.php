<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * Builds URLs for media stored outside the public directory.
 *
 * Every URL is signed, expires, and carries a fingerprint of the visitor's
 * session, so a copied link stops working in another browser or after expiry.
 */
class ProtectedMedia
{
    public static function root(string $path = ''): string
    {
        $root = rtrim(config('media.root'), '/\\');

        return $path === '' ? $root : $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Image source for content config: full URLs pass through, anything else is
     * treated as a protected image under media/images.
     */
    public static function src(?string $image, int $width = 1600): ?string
    {
        if ($image === null || $image === '') {
            return null;
        }

        return str_starts_with($image, 'http') ? $image : self::imageUrl($image, $width);
    }

    /**
     * Signed URL for an image under media/images, or null when the file is missing.
     * Photos are served as a resized copy (ImageVariants) close to $width pixels wide.
     */
    public static function imageUrl(?string $path, ?int $width = 1600): ?string
    {
        $path = trim((string) $path, '/');

        if ($path === '' || ! is_file(self::root('images/'.$path))) {
            return null;
        }

        if ($width && preg_match('/\.(jpe?g|png|webp)$/i', $path)) {
            $path = ImageVariants::path($path, $width);
        }

        return self::sign('media.image', ['path' => $path]);
    }

    /**
     * Signed HLS playlist URL for an encrypted stream, or null when it hasn't been built.
     */
    public static function streamUrl(?string $name): ?string
    {
        if (! self::isValidName((string) $name) || ! is_file(self::root("streams/{$name}/index.m3u8"))) {
            return null;
        }

        return self::sign('media.playlist', ['stream' => $name]);
    }

    public static function sign(string $route, array $parameters): string
    {
        return URL::temporarySignedRoute(
            $route,
            self::expiresAt(),
            $parameters + ['sid' => self::sessionFingerprint()],
            absolute: false,
        );
    }

    /**
     * Short, non-reversible tag for the current session (the raw session id is never exposed).
     */
    public static function sessionFingerprint(): string
    {
        return substr(hash_hmac('sha256', session()->getId(), (string) config('app.key')), 0, 20);
    }

    public static function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9-]{0,63}$/', $name);
    }

    protected static function expiresAt(): Carbon
    {
        return now()->addMinutes(config('media.url_ttl_minutes'))->startOfHour()->addHour();
    }
}
