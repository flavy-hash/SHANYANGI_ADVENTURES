<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Shanyangi Adventures')</title>
        <meta name="description" content="@yield('description', 'Your local guide to unforgettable Tanzania safaris.')">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,400&display=swap" rel="stylesheet">

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

        @if (config('site.live_chat'))
            @include('partials.chat-widget')
        @endif
    </body>
</html>
