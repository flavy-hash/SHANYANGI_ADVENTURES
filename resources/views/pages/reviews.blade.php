@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Guest Reviews | Shanyangi Adventures')
@section('description', 'Read reviews from travellers who explored Tanzania with Shanyangi Adventures: safaris, Kilimanjaro climbs and Zanzibar holidays.')

@php
    $googleUrl = config('reviews.google_reviews_url');
    $tripadvisorUrl = config('reviews.tripadvisor_url');
@endphp

@section('content')
    <x-page-hero
        title="Guest Reviews"
        kicker="Reviews"
        subtitle="Stories from travellers who explored Tanzania with us: safaris, mountain climbs and island escapes."
        image="https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&w=2000&q=75"
        :crumbs="array_filter([['label' => 'Reviews', 'url' => route('reviews')], $activeLabel ? ['label' => $activeLabel] : null])"
    />

    <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="reviews-page-title">
        <div class="tm-container">
            <h2 id="reviews-page-title" class="sr-only">All reviews</h2>

            {{-- Rating summary + invite --}}
            <div class="tm-reviews-summary">
                @if ($summary['count'])
                    <div class="tm-reviews-score">
                        <p class="tm-reviews-average">{{ number_format($summary['average'], 1) }}</p>
                        <x-review-stars :rating="$summary['average']" />
                        <p class="tm-reviews-count">Based on {{ $summary['count'] }} {{ Str::plural('review', $summary['count']) }}</p>
                    </div>

                    <ul class="tm-reviews-bars" aria-label="Rating breakdown">
                        @foreach ($summary['breakdown'] as $stars => $count)
                            @php($percent = $summary['count'] ? round($count / $summary['count'] * 100) : 0)
                            <li>
                                <span class="tm-reviews-bar-label">{{ $stars }} star</span>
                                <span class="tm-reviews-bar" aria-hidden="true"><span style="width: {{ $percent }}%"></span></span>
                                <span class="tm-reviews-bar-count">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="tm-reviews-invite">
                    <h3>Travelled with us?</h3>
                    <p>We would love to hear how it went. Your review helps other travellers plan their trip.</p>
                    <div class="tm-reviews-invite-actions">
                        @if ($googleUrl)
                            <a href="{{ $googleUrl }}" target="_blank" rel="noopener" class="tm-btn-primary">Review us on Google</a>
                        @endif
                        @if ($tripadvisorUrl)
                            <a href="{{ $tripadvisorUrl }}" target="_blank" rel="noopener" class="tm-btn-outline-dark">Review us on Tripadvisor</a>
                        @endif
                        @unless ($googleUrl || $tripadvisorUrl)
                            <a href="{{ route('contact') }}#request" class="tm-btn-primary">Share your experience</a>
                        @endunless
                    </div>
                </div>
            </div>

            @if ($filters)
                <x-filter-chips :filters="$filters" label="Filter reviews by trip type" />
            @endif

            @if ($reviews->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($reviews as $i => $review)
                        <x-review-card :review="$review" :id="'review-'.$i" />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">
                    @if ($activeLabel)
                        No {{ $activeLabel }} reviews yet. <a href="{{ route('reviews') }}">See all reviews</a>.
                    @else
                        Guest reviews will appear here soon. In the meantime, you can read what travellers say about us on Google and Tripadvisor.
                    @endif
                </p>
            @endif
        </div>
    </section>

    <section class="bg-tm-cream py-14 sm:py-16" aria-label="Review sites">
        <div class="tm-container">
            <p class="tm-section-kicker">Independent Reviews</p>
            <h2 class="tm-section-title">Find Us on Review Sites</h2>
            <div class="tm-divider" aria-hidden="true"></div>
            <x-review-badges fallback="{{ route('reviews') }}" />
        </div>
    </section>

    <x-cta-band title="Ready to write your own story?" text="Tell us about the trip you have in mind, and we will plan a journey worth reviewing." />
@endsection
