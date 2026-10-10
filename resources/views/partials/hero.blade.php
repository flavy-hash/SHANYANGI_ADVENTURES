@use('App\Support\ProtectedMedia')

@php
    // Encrypted stream + poster from protected storage (config/site.php, config/media.php).
    $heroStream = ProtectedMedia::streamUrl(config('site.hero_stream'));
    $heroPoster = ProtectedMedia::imageUrl(config('site.hero_poster'))
        ?? 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=2000&q=80';

    $phrases = ['Welcome to Shanyangi Adventures', 'Your Local Guide to Unforgettable Safaris'];
@endphp

<section class="tm-home-hero" style="background-image: url('{{ $heroPoster }}')" data-hero-parallax data-protected-media>
    <div class="tm-hero-media" aria-hidden="true">
        @if ($heroStream)
            {{-- No src: resources/js/protected-media.js attaches the encrypted HLS stream. --}}
            <video class="tm-hero-media-element" data-stream="{{ $heroStream }}" autoplay muted loop playsinline preload="none"
                   poster="{{ $heroPoster }}" tabindex="-1"
                   controlslist="nodownload noplaybackrate noremoteplayback" disablepictureinpicture disableremoteplayback></video>
        @else
            <img class="tm-hero-media-element" src="{{ $heroPoster }}" alt="" fetchpriority="high" decoding="async" draggable="false">
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

            <div class="tm-home-hero-actions mt-10 flex items-center justify-center gap-3">
                <a href="{{ url('/safaris') }}" class="tm-home-hero-cta tm-btn-outline">Explore Experiences</a>
            </div>
        </div>
    </div>
</section>
