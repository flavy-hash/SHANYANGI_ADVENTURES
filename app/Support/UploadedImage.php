<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Stores images uploaded by visitors on the private "media" disk (served through
 * ProtectedMedia signed URLs).
 *
 * Every image is decoded and re-encoded as a fresh JPEG: this strips metadata such as
 * the GPS location phones embed, fixes the rotation, limits the size, and makes sure
 * no disguised (non-image) content survives.
 */
class UploadedImage
{
    public const MAX_SIDE = 1600;

    public const QUALITY = 82;

    /**
     * @return string|null path on the media disk, e.g. "uploads/reviews/9b1d….jpg"
     */
    public static function store(UploadedFile $file, string $directory): ?string
    {
        try {
            $info = @getimagesize($file->getRealPath());
            // Refuse absurd dimensions before decoding (decompression bombs).
            if (! $info || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > 40_000_000) {
                return null;
            }

            $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
            if (! $image) {
                return null;
            }

            $image = self::orient($image, $file, $info[2]);
            $image = self::fit($image);

            ob_start();
            imagejpeg($image, null, self::QUALITY);
            $jpeg = (string) ob_get_clean();
            imagedestroy($image);

            $path = trim($directory, '/').'/'.Str::uuid().'.jpg';

            return Storage::disk('media')->put($path, $jpeg) ? $path : null;
        } catch (Throwable $e) {
            Log::warning('Uploaded image could not be processed: '.$e->getMessage());

            return null;
        }
    }

    /** Apply the camera's EXIF rotation, then forget the EXIF data. */
    protected static function orient(\GdImage $image, UploadedFile $file, int $type): \GdImage
    {
        if ($type !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }

    /** Scale down to MAX_SIDE and flatten transparency onto white. */
    protected static function fit(\GdImage $image): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::MAX_SIDE / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $canvas;
    }
}
