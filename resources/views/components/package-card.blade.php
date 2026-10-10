@props(['package'])

@use('App\Support\ProtectedMedia')

@php
    $currency = config('packages.currency', '$');
    $href = route('safaris.show', $package['slug']);
@endphp

<article class="tm-package-card">
    <div class="tm-package-media">
        @if ($src = ProtectedMedia::src($package['image'] ?? null))
            <img src="{{ $src }}" alt="" loading="lazy" decoding="async" draggable="false">
        @endif
        <span class="tm-package-category">{{ $package['category'] }}</span>
    </div>

    <div class="tm-package-body">
        @isset($package['tagline'])
            <p class="tm-package-tagline">{{ $package['tagline'] }}</p>
        @endisset

        <h3 class="tm-package-title">
            {{-- Stretched link: the whole card opens the package page. --}}
            <a href="{{ $href }}">{{ $package['title'] }}</a>
        </h3>

        @if (! empty($package['chips']))
            <ul class="tm-package-chips">
                @foreach ($package['chips'] as $chip)
                    <li @class(['tm-chip', 'is-highlight' => $chip['highlight'] ?? false])>{{ $chip['label'] }}</li>
                @endforeach
            </ul>
        @endif

        <p class="tm-package-text">{{ $package['summary'] }}</p>

        <div class="tm-package-footer">
            <div>
                @if ($package['price'] ?? null)
                    <p class="tm-package-price">From {{ $currency }}{{ number_format($package['price']) }}</p>
                    <p class="tm-package-price-note">per person</p>
                @else
                    <p class="tm-package-price">Price on request</p>
                    <p class="tm-package-price-note">tailored to your group</p>
                @endif
            </div>
            <span class="tm-package-view" aria-hidden="true">
                Read more
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
            </span>
        </div>
    </div>
</article>
