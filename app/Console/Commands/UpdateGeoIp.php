<?php

namespace App\Console\Commands;

use App\Support\GeoIp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use MaxMind\Db\Reader;
use Throwable;

/**
 * Downloads the free DB-IP "IP to Country Lite" database used to show visitor
 * countries in the admin. Scheduled monthly (routes/console.php).
 */
class UpdateGeoIp extends Command
{
    protected $signature = 'analytics:update-geoip';

    protected $description = 'Download the latest IP-to-country database for the visitor activity log';

    public function handle(): int
    {
        $target = config('analytics.geoip.database');
        File::ensureDirectoryExists(dirname($target));

        // A new edition appears at the start of each month; fall back to last month's.
        foreach ([now(), now()->subMonthNoOverflow()] as $month) {
            $url = str_replace('{month}', $month->format('Y-m'), config('analytics.geoip.download_url'));
            $this->line('Downloading '.$url);

            try {
                $response = Http::timeout(120)->get($url);
            } catch (Throwable $e) {
                $this->warn('  '.$e->getMessage());

                continue;
            }

            if (! $response->successful()) {
                $this->warn('  HTTP '.$response->status());

                continue;
            }

            $data = @gzdecode($response->body());
            if ($data === false) {
                $this->warn('  Not a valid gzip file.');

                continue;
            }

            // Write next to the target, check it opens, then swap it in.
            $temp = $target.'.tmp';
            File::put($temp, $data);

            try {
                (new Reader($temp))->close();
            } catch (Throwable $e) {
                File::delete($temp);
                $this->warn('  Downloaded file is not a valid database: '.$e->getMessage());

                continue;
            }

            GeoIp::reset();
            // Rename can fail on Windows while the web server has the old file open.
            if (! @File::move($temp, $target)) {
                File::copy($temp, $target);
                File::delete($temp);
            }
            $this->info('Country database updated ('.round(strlen($data) / 1048576, 1).' MB): '.$target);

            return self::SUCCESS;
        }

        $this->error('Could not download the country database. Visitor countries will show as "Unknown" until it is installed.');

        return self::FAILURE;
    }
}
