<?php

/*
|--------------------------------------------------------------------------
| Site Settings
|--------------------------------------------------------------------------
|
| Contact numbers are used by the mobile bottom navigation (WhatsApp + Call
| buttons); when one is missing, its button falls back to the contact page.
|
*/

return [

    // International format, digits only or with "+", e.g. 255712345678
    'whatsapp' => env('SITE_WHATSAPP'),

    'whatsapp_message' => env('SITE_WHATSAPP_MESSAGE', 'Hi Shanyangi Adventures! I am interested in booking a safari.'),

    // Dialable phone number, e.g. +255712345678
    'phone' => env('SITE_PHONE'),

    // Footer contact details (each line is hidden until set).
    'email' => env('SITE_EMAIL'),

    'address' => env('SITE_ADDRESS'),

    // Office time zone: times shown in the admin (stored as UTC).
    'timezone' => env('SITE_TIMEZONE', 'Africa/Dar_es_Salaam'),

    // Live chat bubble on every page (answered in the admin under Bookings → Live chat).
    'live_chat' => (bool) env('SITE_LIVE_CHAT', true),

    // Footer partner / affiliation logos (files in public/images). 'url' is optional:
    // set it to link a logo, e.g. to your SafariBookings profile.
    'partners' => [
        ['name' => 'SafariBookings', 'logo' => 'images/s-b-logo_pdf.webp', 'url' => env('SITE_SAFARIBOOKINGS_URL')],
        ['name' => 'Tanzania National Parks', 'logo' => 'images/taifa-logo.webp', 'url' => null],
        ['name' => 'Tanzania Tourist Board', 'logo' => 'images/TTB-Logo.webp', 'url' => null],
    ],

    // Footer social icons: only the ones with a URL are shown.
    // Defaults for the admin's Site settings page (App\Support\SocialLinks).
    'social' => [
        'instagram' => env('SITE_INSTAGRAM'),
        'facebook' => env('SITE_FACEBOOK'),
        'linkedin' => env('SITE_LINKEDIN'),
        'tiktok' => env('SITE_TIKTOK'),
        'youtube' => env('SITE_YOUTUBE'),
    ],

    /*
    | Home hero background (protected media, see config/media.php).
    | hero_stream: name of an encrypted stream built with
    |   php artisan media:encrypt-video <file> hero
    | hero_poster: image under media/images, shown while the video loads and
    |   to visitors who prefer reduced motion.
    */
    'hero_stream' => env('SITE_HERO_STREAM', 'hero'),

    'hero_poster' => env('SITE_HERO_POSTER', 'hero-poster.jpg'),

];
