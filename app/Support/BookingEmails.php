<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Where new bookings (trip requests, instant quotes) are emailed, so the office hears
 * about them even when nobody is looking at the admin dashboard.
 *
 * Addresses are set in the admin (Pages → Site settings → Booking emails). Until they are,
 * BOOKINGS_EMAIL from .env is used (comma-separate several), then SITE_EMAIL.
 */
class BookingEmails
{
    public const KEY = 'booking_emails';

    /** @return list<string> */
    public static function recipients(): array
    {
        $saved = (array) (SiteSetting::raw(self::KEY)['emails'] ?? []);
        $fallback = explode(',', (string) (config('site.bookings_email') ?: config('site.email')));

        foreach ([$saved, $fallback] as $list) {
            $emails = array_values(array_unique(array_filter(
                array_map(fn ($email) => strtolower(trim((string) $email)), $list),
                fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
            )));

            if ($emails) {
                return $emails;
            }
        }

        return [];
    }

    /** Addresses as saved in the admin (for the settings form). */
    public static function saved(): array
    {
        return array_values((array) (SiteSetting::raw(self::KEY)['emails'] ?? []));
    }

    /**
     * Email the office. The booking is already saved, so a mail failure must never cost the
     * visitor their submission: it is logged instead. Returns whether the email went out.
     */
    public static function send(Mailable $mail, array $context = []): bool
    {
        if (! $to = self::recipients()) {
            return false;
        }

        try {
            Mail::to($to)->send($mail);

            return true;
        } catch (Throwable $e) {
            Log::error('Booking email failed', [...$context, 'mail' => class_basename($mail), 'error' => $e->getMessage()]);

            return false;
        }
    }
}
