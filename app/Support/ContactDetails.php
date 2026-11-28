<?php

namespace App\Support;

use App\Models\SiteSetting;
use Throwable;

/**
 * Public contact details (contact page, footer, mobile bar, quote PDFs, legal pages).
 *
 * Edited in the admin under Pages → Site settings → Contact details. Once saved there, those
 * values replace config/site.php (and the SITE_* lines in .env) for the whole site.
 */
class ContactDetails
{
    public const KEY = 'contact';

    public const FIELDS = ['email', 'phone', 'whatsapp', 'address'];

    /** Copy the admin values into config('site.*') so every page uses them. Called on boot. */
    public static function apply(): void
    {
        try {
            $saved = SiteSetting::raw(self::KEY);
        } catch (Throwable) {
            return; // no database yet (fresh install, composer scripts): keep .env values
        }

        if (is_array($saved)) {
            config(collect(self::FIELDS)
                ->filter(fn ($field) => array_key_exists($field, $saved))
                ->mapWithKeys(fn ($field) => ['site.'.$field => filled($saved[$field]) ? trim((string) $saved[$field]) : null])
                ->all());
        }
    }

    /** Current values as the site shows them. */
    public static function current(): array
    {
        return collect(self::FIELDS)->mapWithKeys(fn ($field) => [$field => config('site.'.$field)])->all();
    }

    /**
     * A number in readable international form, e.g. 255787156775 → +255 787 156 775.
     * Other formats are shown as typed.
     */
    public static function display(?string $number): ?string
    {
        if (blank($number)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $number);

        return preg_match('/^255(\d{3})(\d{3})(\d{3})$/', $digits, $m)
            ? "+255 {$m[1]} {$m[2]} {$m[3]}"
            : trim($number);
    }
}
