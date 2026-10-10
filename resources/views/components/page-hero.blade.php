@props([
    'title',
    'kicker' => null,
    'subtitle' => null,
    'image' => null,
    'crumbs' => [],   // [['label' => 'Blog', 'url' => '/blog'], ['label' => 'Safari']]
    'badge' => null,  // outlined pill above the title, e.g. "Safari"
    'location' => null,
    'align' => 'center',
])

@use('App\Support\ProtectedMedia')

@php($src = ProtectedMedia::src($image))

{{-- Inner-page banner: photo, dark gradient, centred title. The transparent header sits on top of it. --}}
<section @class(['tm-page-hero', 'is-left' => $align === 'left']) data-protected-media>
    @if ($src)
        <img src="{{ $src }}" alt="" class="tm-page-hero-img" fetchpriority="high" decoding="async" draggable="false">
    @endif

    <div class="tm-page-hero-inner tm-container">
        @if ($crumbs)
            <nav aria-label="Breadcrumb">
                <ol class="tm-breadcrumb">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    @foreach ($crumbs as $crumb)
                        <li>
                            @if (! $loop->last && ! empty($crumb['url']))
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @else
                                <span aria-current="page">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($kicker)
            <p class="tm-page-hero-kicker">{{ $kicker }}</p>
        @endif
        @if ($badge)
            <span class="tm-page-hero-badge">{{ $badge }}</span>
        @endif
        <h1 class="tm-page-hero-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="tm-page-hero-subtitle">{{ $subtitle }}</p>
        @endif
        @if ($location)
            <p class="tm-page-hero-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 1 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                {{ $location }}
            </p>
        @endif
    </div>
</section>
