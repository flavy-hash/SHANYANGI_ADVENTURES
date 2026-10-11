<?php

namespace App\Console\Commands;

use App\Support\ImageVariants;
use App\Support\ProtectedMedia;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

/**
 * Pre-builds the resized copies of every protected image. Optional: copies are also
 * made on first request, but running this after a deploy or bulk upload means no
 * visitor waits for them.
 */
class MakeImageVariants extends Command
{
    protected $signature = 'media:variants {--fresh : Rebuild copies that already exist}';

    protected $description = 'Build the smaller (WebP) copies of protected images used on the website';

    public function handle(): int
    {
        $root = ProtectedMedia::root('images');
        if (! is_dir($root)) {
            $this->warn('No images folder at '.$root);

            return self::SUCCESS;
        }

        $files = Finder::create()->files()->in($root)->exclude('_v')->name('/\.(jpe?g|png|webp)$/i');
        $format = ImageVariants::format();
        $made = $failed = 0;

        $this->withProgressBar(iterator_to_array($files), function ($file) use ($root, $format, &$made, &$failed) {
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));

            foreach (ImageVariants::WIDTHS as $width) {
                $target = ProtectedMedia::root('images/'.ImageVariants::path($relative, $width));
                if ($this->option('fresh') && is_file($target)) {
                    unlink($target);
                }
                ImageVariants::make($file->getPathname(), $target, $width, $format) ? $made++ : $failed++;
            }
        });

        $this->newLine();
        $this->info("Image copies ready ({$format}): {$made}".($failed ? ", failed: {$failed}" : ''));

        return self::SUCCESS;
    }
}
