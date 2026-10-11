<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Smaller copies of protected images (WebP when the server supports it), so a card that
 * is 400px wide doesn't download a 1600px JPEG. Stored next to the originals under
 * media/images/_v/{width}/… and served through the same signed route (ProtectedMedia).
 */
class ImageVariants
{
    /** Widths the site asks for; anything else is rejected. */
    public const WIDTHS = [480, 800, 1200, 1600];

    public const QUALITY = 78;

    public static function format(): string
    {
        return function_exists('imagewebp') ? 'webp' : 'jpg';
    }

    /** Width from the list that best fits the requested one. */
    public static function nearest(int $width): int
    {
        foreach (self::WIDTHS as $candidate) {
            if ($candidate >= $width) {
                return $candidate;
            }
        }

        return self::WIDTHS[array_key_last(self::WIDTHS)];
    }

    /** "seed/x.jpg" at 800 → "_v/800/seed/x.jpg.webp" */
    public static function path(string $original, int $width): string
    {
        return '_v/'.self::nearest($width).'/'.ltrim($original, '/').'.'.self::format();
    }

    public static function isVariant(string $path): bool
    {
        return str_starts_with($path, '_v/');
    }

    /**
     * Parse a variant path into [width, original path, format], or null if it isn't one we make.
     *
     * @return array{0: int, 1: string, 2: string}|null
     */
    public static function parse(string $path): ?array
    {
        if (! preg_match('#^_v/(\d{3,4})/(.+\.(?:jpe?g|png|webp))\.(webp|jpg)$#i', $path, $m)) {
            return null;
        }

        return in_array((int) $m[1], self::WIDTHS, true) ? [(int) $m[1], $m[2], strtolower($m[3])] : null;
    }

    /**
     * Create (or refresh) the variant file. Returns false when the original can't be read.
     */
    public static function make(string $originalFile, string $targetFile, int $width, string $format): bool
    {
        try {
            if (is_file($targetFile) && filemtime($targetFile) >= filemtime($originalFile)) {
                return true;
            }

            $info = @getimagesize($originalFile);
            if (! $info || $info[0] * $info[1] > 40_000_000) {
                return false;
            }

            $source = @imagecreatefromstring((string) file_get_contents($originalFile));
            if (! $source) {
                return false;
            }

            [$w, $h] = [imagesx($source), imagesy($source)];
            $scale = min(1, $width / $w);
            [$nw, $nh] = [max(1, (int) round($w * $scale)), max(1, (int) round($h * $scale))];

            $canvas = imagecreatetruecolor($nw, $nh);
            if ($format === 'webp') {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
            } else {
                imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
            }
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($source);

            if (! is_dir(dirname($targetFile))) {
                mkdir(dirname($targetFile), 0775, true);
            }

            // Write to a temp file and swap it in, so a half-written file is never served.
            $temp = $targetFile.'.'.bin2hex(random_bytes(4)).'.tmp';
            $ok = $format === 'webp' ? imagewebp($canvas, $temp, self::QUALITY) : imagejpeg($canvas, $temp, self::QUALITY + 4);
            imagedestroy($canvas);

            return $ok && rename($temp, $targetFile);
        } catch (Throwable $e) {
            Log::warning('Image variant failed: '.$e->getMessage(), ['file' => $originalFile]);

            return false;
        }
    }
}
