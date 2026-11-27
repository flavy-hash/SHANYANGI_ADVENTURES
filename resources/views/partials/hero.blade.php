@use('App\Support\HomePage')

@php
    // Headlines, buttons, photo and video are edited in the admin (Pages → Home page).
    $hero = HomePage::hero();
    $heroPoster = $hero['poster'];
    $phrases = $hero['phrases'];
@endphp

{{-- The poster is the first thing people see: fetch it straight away, before CSS and scripts. --}}
@push('head')
    <link rel="preload" as="image" href="{{ $heroPoster }}" fetchpriority="high">
@endpush

<section class="tm-home-hero" style="background-image: url('{{ $heroPoster }}')" data-hero-parallax data-protected-media>
    <div class="tm-hero-media" aria-hidden="true">
        {{-- Poster as a real image: shows first (and counts as the page's main content); the video fades in on top once it plays. --}}
        <img class="tm-hero-media-element" src="{{ $heroPoster }}" alt="" fetchpriority="high" decoding="async" draggable="false">
        @if ($hero['video'])
            {{-- No src: resources/js/protected-media.js attaches the encrypted HLS stream, or the signed video file. --}}
            <video class="tm-hero-media-element"
                   @if ($hero['video']['type'] === 'stream') data-stream="{{ $hero['video']['url'] }}" @else data-src="{{ $hero['video']['url'] }}" @endif
                   autoplay muted loop playsinline preload="none"
                   poster="{{ $heroPoster }}" tabindex="-1"
                   controlslist="nodownload noplaybackrate noremoteplayback" disablepictureinpicture disableremoteplayback></video>
        @endif
    </div>

    <div class="tm-home-hero-inner tm-container">
        <div class="tm-home-hero-content mx-auto max-w-3xl text-center text-white">
            <h1 class="tm-home-hero-title text-3xl font-semibold tracking-tight sm:text-5xl">
                {{-- Real (screen-reader + Google Translate) copy; the typewriter re-reads it each cycle so it animates in the chosen language. --}}
                @foreach ($phrases as $phrase)
                    <span class="sr-only" data-hero-phrase>{{ $phrase }}.</span>
                @endforeach
                <span class="tm-hero-typewriter notranslate" translate="no" data-hero-typewriter aria-hidden="true">{{ $phrases[0] }}</span>
            </h1>

            @if ($hero['subtitle'])
                <p class="tm-home-hero-subtitle">{{ $hero['subtitle'] }}</p>
            @endif

            @if ($hero['buttons'])
                <div class="tm-home-hero-actions mt-10 flex flex-wrap items-center justify-center gap-3">
                    @foreach ($hero['buttons'] as $button)
                        <a href="{{ $button['url'] }}" class="tm-home-hero-cta tm-btn-outline">{{ $button['label'] }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
