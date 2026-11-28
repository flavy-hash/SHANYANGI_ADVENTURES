<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        {{-- Search engines and link previews (App\Support\Seo). Pages set: title (without the brand),
             description, and optionally og_image (a public image URL) and og_type. --}}
        @php
            $seoTitle = \App\Support\Seo::title($__env->yieldContent('title'));
            $seoDescription = \App\Support\Seo::clean($__env->yieldContent('description', 'Locally owned Tanzanian safaris, Kilimanjaro climbs and Zanzibar escapes, planned around you.'), 300);
            $seoUrl = \App\Support\Seo::canonical();
            $seoImage = \App\Support\Seo::image(\App\Support\Seo::clean($__env->yieldContent('og_image')) ?: null);
        @endphp
        <title>{{ $seoTitle }}</title>
        <meta name="description" content="{{ $seoDescription }}">
        <link rel="canonical" href="{{ $seoUrl }}">

        <meta property="og:type" content="{{ trim($__env->yieldContent('og_type', 'website')) }}">
        <meta property="og:site_name" content="{{ \App\Support\Seo::brand() }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">
        <meta property="og:image" content="{{ $seoImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ $seoImage }}">

        @if ($verification = config('seo.google_verification'))
            <meta name="google-site-verification" content="{{ $verification }}">
        @endif
        @if ($verification = config('seo.bing_verification'))
            <meta name="msvalidate.01" content="{{ $verification }}">
        @endif

        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::organization()) !!}</script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,400&display=swap" rel="stylesheet">

        @stack('head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-tm-beige font-sans text-tm-ink antialiased">
        @include('partials.header')

        <main id="top">
            @yield('content')
        </main>

        @include('partials.newsletter')
        @include('partials.footer')

        @include('partials.bottom-nav')

        @include('partials.whatsapp-float')

        @if (config('site.live_chat'))
            @include('partials.chat-widget')
        @endif
    </body>
</html>
