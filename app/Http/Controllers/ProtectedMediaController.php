<?php

namespace App\Http\Controllers;

use App\Support\ImageVariants;
use App\Support\ProtectedMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves images and encrypted HLS streams from outside /public.
 *
 * Routes are behind the `signed` middleware; on top of that every request must
 * come from the same session the URL was issued to, from this site, and not
 * as a top-level page load (so links can't be opened or shared directly).
 */
class ProtectedMediaController extends Controller
{
    public function image(Request $request, string $path): BinaryFileResponse
    {
        $this->guard($request);

        $file = ImageVariants::isVariant($path) ? $this->variant($path) : $this->resolve('images', $path);

        return response()->file($file, $this->cacheHeaders($request) + [
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * A resized copy of an image, created the first time it's asked for.
     */
    protected function variant(string $path): string
    {
        $parsed = ImageVariants::parse($path);
        abort_unless($parsed, 404);
        [$width, $originalPath, $format] = $parsed;

        $original = $this->resolve('images', $originalPath); // also rejects path traversal
        $target = ProtectedMedia::root('images/'.$path);

        // If resizing fails for any reason, fall back to the original image.
        return ImageVariants::make($original, $target, $width, $format) ? $target : $original;
    }

    public function playlist(Request $request, string $stream): Response
    {
        $this->guard($request);

        $playlist = file_get_contents($this->resolve("streams/{$stream}", 'index.m3u8'));

        // Point the key and every segment at fresh signed URLs.
        $lines = array_map(function (string $line) use ($stream) {
            if (str_starts_with($line, '#EXT-X-KEY')) {
                $keyUrl = ProtectedMedia::sign('media.key', ['stream' => $stream]);

                return preg_replace('/URI="[^"]*"/', 'URI="'.$keyUrl.'"', $line);
            }

            if ($line !== '' && ! str_starts_with($line, '#')) {
                return ProtectedMedia::sign('media.segment', ['stream' => $stream, 'segment' => basename(trim($line))]);
            }

            return $line;
        }, preg_split('/\r?\n/', $playlist));

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function segment(Request $request, string $stream, string $segment): BinaryFileResponse
    {
        $this->guard($request);

        return response()->file($this->resolve("streams/{$stream}", $segment), $this->cacheHeaders($request) + [
            'Content-Type' => 'video/mp2t',
        ]);
    }

    public function key(Request $request, string $stream): Response
    {
        $this->guard($request);

        return response(file_get_contents($this->resolve("streams/{$stream}", 'enc.key')), 200, [
            'Content-Type' => 'application/octet-stream',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    protected function guard(Request $request): void
    {
        // URL must have been issued to this visitor's session.
        abort_unless(hash_equals(ProtectedMedia::sessionFingerprint(), (string) $request->query('sid')), 403);

        // Block other sites embedding/hotlinking, and opening the URL directly in a tab.
        abort_if($request->header('Sec-Fetch-Site') === 'cross-site', 403);
        abort_if($request->header('Sec-Fetch-Dest') === 'document', 403);
    }

    /**
     * Map a request path to a real file inside the media root, rejecting traversal.
     */
    protected function resolve(string $directory, string $path): string
    {
        $base = realpath(ProtectedMedia::root($directory));
        $file = $base ? realpath($base.DIRECTORY_SEPARATOR.$path) : false;

        abort_unless($file && is_file($file) && str_starts_with($file, $base.DIRECTORY_SEPARATOR), 404);

        return $file;
    }

    protected function cacheHeaders(Request $request): array
    {
        // Cache in the browser until the signed URL expires; the URL stays the same on
        // every page of the visit, so each image is downloaded once.
        $maxAge = max(60, (int) $request->query('expires') - time());

        return [
            'Cache-Control' => 'private, max-age='.$maxAge,
            // Without this, "open image in new tab" is answered from the browser cache
            // and never reaches the Sec-Fetch-Dest check in guard(). (Not "Cookie": the
            // XSRF cookie changes on every page, which made browsers re-download images.)
            'Vary' => 'Sec-Fetch-Dest, Sec-Fetch-Site',
            'X-Content-Type-Options' => 'nosniff',
        ];
    }
}
