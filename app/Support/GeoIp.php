<?php

namespace App\Support;

use Illuminate\Http\Request;
use Locale;
use MaxMind\Db\Reader;
use Throwable;

/**
 * Visitor country from the request: Cloudflare's CF-IPCountry header when present,
 * otherwise a lookup in the local DB-IP database (config/analytics.php). Only the
 * two-letter country code leaves this class; nothing is sent to outside services.
 */
class GeoIp
{
    protected static ?Reader $reader = null;

    protected static bool $unavailable = false;

    public static function country(Request $request): ?string
    {
        $header = strtoupper((string) $request->header('CF-IPCountry'));
        if (preg_match('/^[A-Z]{2}$/', $header) && ! in_array($header, ['XX', 'T1'], true)) {
            return $header;
        }

        return self::lookup((string) $request->ip());
    }

    public static function lookup(string $ip): ?string
    {
        // Local and private network addresses have no country.
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        try {
            $record = self::reader()?->get($ip);
        } catch (Throwable) {
            return null;
        }

        $code = $record['country']['iso_code'] ?? null;

        return is_string($code) && preg_match('/^[A-Z]{2}$/', $code) ? $code : null;
    }

    /**
     * "Tanzania" for "TZ" (English names from PHP intl).
     */
    public static function name(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        $name = class_exists(Locale::class) ? Locale::getDisplayRegion('-'.$code, 'en') : '';

        return $name !== '' && $name !== $code ? $name : $code;
    }

    /**
     * Flag emoji for a country code (regional indicator letters).
     */
    public static function flag(?string $code): string
    {
        if (! $code || ! preg_match('/^[A-Z]{2}$/', $code)) {
            return '';
        }

        return mb_chr(0x1F1E6 + ord($code[0]) - 65).mb_chr(0x1F1E6 + ord($code[1]) - 65);
    }

    /** Forget the open database (after an update, and in tests). */
    public static function reset(): void
    {
        self::$reader?->close();
        self::$reader = null;
        self::$unavailable = false;
    }

    protected static function reader(): ?Reader
    {
        if (self::$reader || self::$unavailable) {
            return self::$reader;
        }

        $path = config('analytics.geoip.database');
        if (! $path || ! is_file($path)) {
            self::$unavailable = true;

            return null;
        }

        try {
            return self::$reader = new Reader($path);
        } catch (Throwable) {
            self::$unavailable = true;

            return null;
        }
    }
}
