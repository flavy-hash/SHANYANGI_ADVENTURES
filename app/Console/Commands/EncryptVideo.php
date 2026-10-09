<?php

namespace App\Console\Commands;

use App\Support\ProtectedMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class EncryptVideo extends Command
{
    protected $signature = 'media:encrypt-video
        {source : Path to the source video (absolute, or relative to the project root)}
        {name : Stream name used in config, e.g. "hero" (lowercase letters, digits, dashes)}
        {--crf=23 : x264 quality (lower = better quality, bigger files)}
        {--max-width=1920 : Downscale wider videos to this width}
        {--segment=4 : Segment length in seconds}
        {--keep-audio : Keep the audio track (background videos drop it by default)}';

    protected $description = 'Convert a video into an AES-128 encrypted HLS stream stored outside /public';

    public function handle(): int
    {
        $source = $this->argument('source');
        $source = is_file($source) ? realpath($source) : realpath(base_path($source));
        $name = $this->argument('name');

        if (! $source) {
            $this->error("Source video not found: {$this->argument('source')}");

            return self::FAILURE;
        }

        if (! ProtectedMedia::isValidName($name)) {
            $this->error('Name must be lowercase letters, digits and dashes (e.g. "hero").');

            return self::FAILURE;
        }

        $dir = ProtectedMedia::root("streams/{$name}");
        File::deleteDirectory($dir);
        File::ensureDirectoryExists($dir);

        // Fresh key + IV for every build; the key file is only ever served via the signed key route.
        $keyFile = $dir.DIRECTORY_SEPARATOR.'enc.key';
        File::put($keyFile, random_bytes(16));
        $keyInfo = $dir.DIRECTORY_SEPARATOR.'keyinfo.txt';
        File::put($keyInfo, implode("\n", ['key', $keyFile, bin2hex(random_bytes(16))])."\n");

        $maxWidth = (int) $this->option('max-width');
        $command = [
            config('media.ffmpeg'), '-y', '-hide_banner', '-loglevel', 'error',
            '-i', $source,
            '-c:v', 'libx264', '-preset', 'medium', '-crf', (string) (int) $this->option('crf'),
            '-pix_fmt', 'yuv420p', '-profile:v', 'high',
            '-vf', "scale='min({$maxWidth},iw)':-2",
            // Fixed keyframe interval so every segment starts cleanly.
            '-force_key_frames', 'expr:gte(t,n_forced*'.(int) $this->option('segment').')',
            ...($this->option('keep-audio') ? ['-c:a', 'aac', '-b:a', '128k'] : ['-an']),
            '-f', 'hls',
            '-hls_time', (string) (int) $this->option('segment'),
            '-hls_playlist_type', 'vod',
            '-hls_key_info_file', $keyInfo,
            '-hls_segment_filename', $dir.DIRECTORY_SEPARATOR.'seg_%03d.ts',
            $dir.DIRECTORY_SEPARATOR.'index.m3u8',
        ];

        $this->info("Encrypting {$source} ...");
        $result = Process::timeout(3600)->run($command);
        File::delete($keyInfo);

        if ($result->failed()) {
            File::deleteDirectory($dir);
            $this->error(trim($result->errorOutput()) ?: 'ffmpeg failed. Is it installed? Set FFMPEG_BINARY in .env if it is not on PATH.');

            return self::FAILURE;
        }

        $segments = count(File::glob($dir.DIRECTORY_SEPARATOR.'seg_*.ts'));
        $size = collect(File::files($dir))->sum(fn ($f) => $f->getSize());
        $this->info(sprintf('Done: %d encrypted segments (%.1f MB) in %s', $segments, $size / 1048576, $dir));

        return self::SUCCESS;
    }
}
