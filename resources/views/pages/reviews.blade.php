@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Guest Reviews | Shanyangi Adventures')
@section('description', 'Read reviews from travellers who explored Tanzania with Shanyangi Adventures: safaris, Kilimanjaro climbs and Zanzibar holidays.')

@php
    $googleUrl = config('reviews.google_reviews_url');
    $tripadvisorUrl = config('reviews.tripadvisor_url');
    $reviewErrors = $errors->getBag('review');
    $reviewSent = session('review_sent');
    $trips = \App\Http\Controllers\ReviewController::tripOptions();
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
                        <button type="button" class="tm-btn-primary" data-dialog-open="write-review">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m2.7 14.06-.7 3.18a.6.6 0 0 0 .72.72l3.18-.7a3 3 0 0 0 1.47-.81l8.83-8.83a2.4 2.4 0 0 0-3.39-3.39L4 13.06a3 3 0 0 0-.8 1Z"/></svg>
                            Write a Review
                        </button>
                        @if ($googleUrl)
                            <a href="{{ $googleUrl }}" target="_blank" rel="noopener" class="tm-btn-outline-dark">Review us on Google</a>
                        @endif
                        @if ($tripadvisorUrl)
                            <a href="{{ $tripadvisorUrl }}" target="_blank" rel="noopener" class="tm-btn-outline-dark">Review us on Tripadvisor</a>
                        @endif
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
                        Guest reviews will appear here soon. Travelled with us? <button type="button" class="tm-link-button" data-dialog-open="write-review">Be the first to write a review</button>.
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

    {{-- Write a review (ReviewController): saved unpublished until approved in the admin --}}
    <dialog id="write-review" class="tm-review-dialog tm-quote-dialog" aria-labelledby="write-review-title"
            @if ($reviewSent || $reviewErrors->any()) data-open-on-load @endif>
        <button type="button" class="tm-review-dialog-close" data-dialog-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/></svg>
        </button>

        @if ($reviewSent)
            <div class="tm-form-success" role="status">
                <span class="tm-feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L19 7"/></svg>
                </span>
                <h2 id="write-review-title" class="tm-form-title">Asante sana{{ is_string($reviewSent) ? ', '.$reviewSent : '' }}!</h2>
                <p>Thank you for sharing your experience. Your review will appear on this page once our team has checked it, usually within a day or two.</p>
                <button type="button" class="tm-btn-primary tm-form-submit" data-dialog-close>Close</button>
            </div>
        @else
            <p class="tm-quote-kicker">Your Review</p>
            <h2 id="write-review-title" class="tm-form-title">How Was Your Trip?</h2>
            <p class="tm-form-intro">Fields marked <span class="tm-required">*</span> are required. Your email is never shown; we only use it if we need to check something about your review.</p>

            @if ($reviewErrors->any())
                <div class="tm-form-alert" role="alert">Please check the highlighted fields below.</div>
            @endif

            <form method="POST" action="{{ route('reviews.store') }}" class="tm-form" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Honeypot: hidden from people, filled by bots. --}}
                <div class="tm-newsletter-hp" aria-hidden="true">
                    <label for="review-website">Website</label>
                    <input type="text" id="review-website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <fieldset class="tm-field">
                    <legend class="tm-label">Your rating <span class="tm-required" aria-hidden="true">*</span></legend>
                    <div class="tm-star-input">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="review-rating-{{ $i }}" name="rating" value="{{ $i }}" @checked((int) old('rating') === $i)>
                            <label for="review-rating-{{ $i }}" title="{{ $i }} {{ Str::plural('star', $i) }}">
                                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.87 2.6a.97.97 0 0 0-1.74 0L7.2 6.5l-4.3.63a.97.97 0 0 0-.54 1.65l3.11 3.03-.73 4.28a.97.97 0 0 0 1.41 1.02L10 15.09l3.85 2.02a.97.97 0 0 0 1.41-1.02l-.73-4.28 3.11-3.03a.97.97 0 0 0-.54-1.65l-4.3-.63-1.93-3.9Z"/></svg>
                                <span class="sr-only">{{ $i }} {{ Str::plural('star', $i) }}</span>
                            </label>
                        @endfor
                    </div>
                    @if ($reviewErrors->has('rating')) <p class="tm-field-error">{{ $reviewErrors->first('rating') }}</p> @endif
                </fieldset>

                <div class="tm-form-row">
                    <x-form-field name="name" label="Your name" required autocomplete="name" :bag="$reviewErrors" />
                    <x-form-field name="email" type="email" label="Email (not shown)" required autocomplete="email" :bag="$reviewErrors" />
                </div>

                <div class="tm-form-row">
                    <x-form-field name="country" label="Country" autocomplete="country-name" :bag="$reviewErrors" />
                    <x-form-field name="travelled_on" type="month" label="When did you travel?" :max="now()->format('Y-m')" :bag="$reviewErrors" />
                </div>

                <div class="tm-field">
                    <label for="review-trip" class="tm-label">Which trip?</label>
                    <select id="review-trip" name="package_id" @class(['tm-input', 'is-invalid' => $reviewErrors->has('package_id')])>
                        <option value="">Choose a trip (optional)</option>
                        @foreach ($trips as $id => $title)
                            <option value="{{ $id }}" @selected((string) old('package_id') === (string) $id)>{{ $title }}</option>
                        @endforeach
                    </select>
                    @if ($reviewErrors->has('package_id')) <p class="tm-field-error">{{ $reviewErrors->first('package_id') }}</p> @endif
                </div>

                <x-form-field name="title" label="Title of your review" required maxlength="150" placeholder="e.g. The migration was unforgettable" :bag="$reviewErrors" />

                <div class="tm-field">
                    <label for="review-body" class="tm-label">Your review <span class="tm-required" aria-hidden="true">*</span></label>
                    <textarea id="review-body" name="body" rows="5" maxlength="3000" required
                              @class(['tm-input', 'is-invalid' => $reviewErrors->has('body')])
                              placeholder="What did you enjoy most? How were your guide, the vehicle and the places you stayed?">{{ old('body') }}</textarea>
                    @if ($reviewErrors->has('body')) <p class="tm-field-error">{{ $reviewErrors->first('body') }}</p> @endif
                </div>

                @php($photoError = $reviewErrors->first('photos') ?: collect($reviewErrors->getMessages())->first(fn ($m, $key) => str_starts_with($key, 'photos.'))[0] ?? null)
                <div class="tm-field" data-photo-picker data-max="{{ \App\Http\Controllers\ReviewController::MAX_PHOTOS }}">
                    <span class="tm-label">Photos from your trip <span class="tm-optional">(optional, up to {{ \App\Http\Controllers\ReviewController::MAX_PHOTOS }})</span></span>
                    <label for="review-photos" @class(['tm-photo-drop', 'is-invalid' => $photoError])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="8.5" cy="10" r="1.6"/><path d="m21 16-5-5-8 8"/></svg>
                        <span><strong>Choose photos</strong> or drag them here</span>
                        <small>JPG, PNG or WebP, up to {{ \App\Http\Controllers\ReviewController::MAX_PHOTO_KB / 1024 }} MB each</small>
                    </label>
                    <input id="review-photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple class="tm-photo-input">
                    <ul class="tm-photo-previews" data-photo-previews aria-live="polite"></ul>
                    <p class="tm-field-error" data-photo-error @if (! $photoError) hidden @endif>{{ $photoError }}</p>
                    @if ($reviewErrors->any() && ! $photoError)
                        <p class="tm-quote-small">If you had chosen photos, please add them again.</p>
                    @endif
                </div>

                <button type="submit" class="tm-btn-primary tm-form-submit">Submit Review</button>
                <p class="tm-quote-small">Reviews are checked by our team before they appear on the website.</p>
            </form>
        @endif
    </dialog>

    <x-cta-band title="Ready to write your own story?" text="Tell us about the trip you have in mind, and we will plan a journey worth reviewing." />
@endsection
