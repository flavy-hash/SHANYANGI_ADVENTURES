@props(['review', 'id'])

@use('App\Support\ProtectedMedia')
@use('Illuminate\Support\Carbon')

@php
    $initials = collect(preg_split('/\s+/', trim($review['name'])))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $date = ! empty($review['date']) ? Carbon::parse($review['date'])->format('M Y') : null;

    // Link the package name to its page when the package is live (see App\Support\Reviews::card).
    $packageSlug = $review['package_slug'] ?? null;
@endphp

<article class="tm-review-card">
    @if ($review['sample'] ?? false)
        <span class="tm-review-sample" title="Placeholder: hidden in production">Sample</span>
    @endif

    @if (! empty($review['images']))
        <div class="tm-review-photos">
            @foreach (array_slice($review['images'], 0, 2) as $image)
                <img src="{{ ProtectedMedia::src($image) }}" alt="" loading="lazy" decoding="async" draggable="false">
            @endforeach
        </div>
    @endif

    <div class="tm-review-author">
        <span class="tm-review-avatar" aria-hidden="true">{{ $initials }}</span>
        <div class="min-w-0">
            <x-review-stars :rating="$review['rating'] ?? 5" />
            <p class="tm-review-meta">
                <strong class="notranslate" translate="no">{{ $review['name'] }}</strong>
                <span>/ {{ $review['country'] }}@if ($date) · {{ $date }}@endif</span>
            </p>
        </div>
    </div>

    <h3 class="tm-review-title">{{ $review['title'] }}</h3>
    <p class="tm-review-text">{{ $review['text'] }}</p>

    <div class="tm-review-footer">
        <button type="button" class="tm-review-read" data-dialog-open="{{ $id }}">Read Review</button>
        @isset($review['package'])
            @if ($packageSlug)
                <a href="{{ route('safaris.show', $packageSlug) }}" class="tm-review-package">{{ $review['package'] }}</a>
            @else
                <span class="tm-review-package">{{ $review['package'] }}</span>
            @endif
        @endisset
    </div>
</article>

<dialog id="{{ $id }}" class="tm-review-dialog" aria-labelledby="{{ $id }}-title">
    <button type="button" class="tm-review-dialog-close" data-dialog-close aria-label="Close review">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/></svg>
    </button>

    <div class="tm-review-author">
        <span class="tm-review-avatar" aria-hidden="true">{{ $initials }}</span>
        <div class="min-w-0">
            <x-review-stars :rating="$review['rating'] ?? 5" />
            <p class="tm-review-meta">
                <strong class="notranslate" translate="no">{{ $review['name'] }}</strong>
                <span>/ {{ $review['country'] }}@if ($date) · {{ $date }}@endif</span>
            </p>
        </div>
    </div>

    <h3 id="{{ $id }}-title" class="tm-review-title">{{ $review['title'] }}</h3>
    <p class="tm-review-dialog-text">{{ $review['text'] }}</p>

    @if (! empty($review['images']))
        <div class="tm-review-dialog-photos">
            @foreach ($review['images'] as $image)
                <img src="{{ ProtectedMedia::src($image) }}" alt="" loading="lazy" decoding="async" draggable="false">
            @endforeach
        </div>
    @endif

    <div class="tm-review-footer">
        @isset($review['package'])
            <span class="tm-review-package">{{ $review['package'] }}</span>
        @endisset
        @isset($review['url'])
            <a href="{{ $review['url'] }}" class="tm-review-read" target="_blank" rel="noopener">
                View original
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 0 1.06 0l7.22-7.22v5.69a.75.75 0 0 0 1.5 0v-7.5a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0 0 1.5h5.69l-7.22 7.22a.75.75 0 0 0 0 1.06Z" clip-rule="evenodd"/></svg>
            </a>
        @endisset
    </div>
</dialog>
