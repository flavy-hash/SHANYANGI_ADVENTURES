@props(['fallback' => '#reviews'])

@php
    $tripadvisorUrl = config('reviews.tripadvisor_url');
    $googleUrl = config('reviews.google_reviews_url');
    $external = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 0 1.06 0l7.22-7.22v5.69a.75.75 0 0 0 1.5 0v-7.5a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0 0 1.5h5.69l-7.22 7.22a.75.75 0 0 0 0 1.06Z" clip-rule="evenodd"/></svg>';
@endphp

{{-- Tripadvisor + Google badges. Whole badge is the link; until a URL is set in .env it points to $fallback. --}}
<div {{ $attributes->class('tm-review-badges') }}>
    <a href="{{ $tripadvisorUrl ?: $fallback }}" class="tm-review-badge"
       @if ($tripadvisorUrl) target="_blank" rel="noopener" @endif>
        <img src="{{ asset('images/tripadvisor.png') }}" alt="Tripadvisor Travelers' Choice" class="tm-review-badge-tripadvisor" loading="lazy" decoding="async" draggable="false">
        <span class="tm-review-badge-link">Read our reviews on Tripadvisor {!! $external !!}</span>
    </a>

    <a href="{{ $googleUrl ?: $fallback }}" class="tm-review-badge"
       @if ($googleUrl) target="_blank" rel="noopener" @endif>
        <span class="tm-review-badge-google notranslate" translate="no">
            <img src="{{ asset('images/google.svg') }}" alt="" width="48" height="48" loading="lazy" decoding="async" draggable="false">
            <span class="tm-review-badge-google-name"><span class="g-blue">G</span><span class="g-red">o</span><span class="g-yellow">o</span><span class="g-blue">g</span><span class="g-green">l</span><span class="g-red">e</span> <span class="text-slate-600">Reviews</span></span>
        </span>
        <span class="tm-review-badge-link">Read our reviews on Google {!! $external !!}</span>
    </a>
</div>
