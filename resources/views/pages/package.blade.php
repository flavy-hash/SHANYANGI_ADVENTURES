@extends('layouts.app')

@use('App\Support\ProtectedMedia')

@php
    $currency = config('packages.currency', '$');
    $days = $package['itinerary'] ?? [];
    $bookUrl = route('contact', ['trip' => $package['title']]).'#request';
    $whatsapp = preg_replace('/\D/', '', (string) config('site.whatsapp'));
    $askUrl = $whatsapp
        ? 'https://wa.me/'.$whatsapp.'?text='.rawurlencode('Hi! I have a question about the '.$package['title'].' package.')
        : $bookUrl;
    $durationLabel = $package->duration_label;
    $canQuote = ($package['price'] ?? 0) > 0;
    $quoteErrors = $errors->getBag('quote');
    $instantQuote = session('instant_quote');
@endphp

@section('title', $package['title'].' | Shanyangi Adventures')
@section('description', $package['summary'])

@section('content')
    <x-page-hero
        align="left"
        :title="$package['title']"
        :badge="$package['category']"
        :subtitle="$package['summary']"
        :location="$package['location'] ?? null"
        :image="$package['image']"
        :crumbs="[['label' => 'Safaris', 'url' => route('safaris')], ['label' => $package['title']]]"
    />

    <section class="bg-tm-beige py-14 sm:py-20">
        <div class="tm-container">
            <div class="tm-package-layout">
                <div class="min-w-0">
                    {{-- Overview --}}
                    <p class="tm-package-kicker">{{ $package['title'] }}@if ($durationLabel) · {{ $durationLabel }}@endif</p>
                    <h2 class="tm-package-heading">Package Overview</h2>
                    <div class="tm-package-overview">
                        @foreach ($package->overview_paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    {{-- Day-by-day itinerary --}}
                    @if ($days)
                        <h3 class="tm-package-subheading">Day-by-Day Itinerary</h3>

                        <div class="tm-itinerary">
                            @foreach ($days as $i => $day)
                                <details class="tm-day" @if ($loop->first) open @endif>
                                    <summary class="tm-day-summary">
                                        <span>Day {{ $i + 1 }} <span class="tm-day-dot" aria-hidden="true">•</span> {{ $day['title'] }}</span>
                                        <svg class="tm-day-caret" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                    </summary>

                                    <div class="tm-day-body">
                                        @if ($src = ProtectedMedia::src($day['image'] ?? null))
                                            <figure class="tm-day-photo" data-protected-media>
                                                <img src="{{ $src }}" alt="" loading="lazy" decoding="async" draggable="false">
                                                @if (filled($day['caption'] ?? null))
                                                    <figcaption>{{ $day['caption'] }}</figcaption>
                                                @endif
                                            </figure>
                                        @endif

                                        <p class="tm-day-text">{{ $day['text'] }}</p>

                                        @if (filled($day['note'] ?? null))
                                            <p class="tm-day-note"><strong>Note:</strong> {{ $day['note'] }}</p>
                                        @endif

                                        @if (filled($day['activity']['title'] ?? null))
                                            <div class="tm-day-activity">
                                                <p class="tm-day-activity-label">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.4l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9L12 3Z"/></svg>
                                                    Activity • {{ $day['activity']['title'] }}
                                                </p>
                                                @if ($activitySrc = ProtectedMedia::src($day['activity']['image'] ?? null))
                                                    <div class="tm-day-activity-photo" data-protected-media>
                                                        <img src="{{ $activitySrc }}" alt="" loading="lazy" decoding="async" draggable="false">
                                                    </div>
                                                @endif
                                                @if (filled($day['activity']['text'] ?? null))
                                                    <p class="tm-day-text">{{ $day['activity']['text'] }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="tm-day-meta">
                                            <p class="tm-day-stay">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6M3 14h18M3 18v2M21 18v2M7 10V7a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v3"/></svg>
                                                Day {{ $i + 1 }}@if (filled($day['stay'] ?? null)) <span aria-hidden="true">|</span> Accommodation, {{ $day['stay'] }}@endif
                                            </p>
                                            @if (filled($day['meals'] ?? null))
                                                <p class="tm-day-meals">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3v8M5 3v5a2 2 0 0 0 4 0V3M7 11v10M17 3c-1.7 1.3-2.5 3.3-2.5 6h2.5v12"/></svg>
                                                    Meal plan: <strong>{{ $day['meals'] }}</strong>
                                                </p>
                                            @endif
                                        </div>

                                        @if (! empty($day['options']))
                                            <div class="tm-day-options">
                                                <div>
                                                    <p class="tm-day-options-title">Options based on your package:</p>
                                                    <ul>
                                                        @foreach ($day['options'] as $option)
                                                            <li>
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg>
                                                                <span>
                                                                    <strong>{{ $option['level'] }}</strong>
                                                                    <small>{{ $option['name'] }}</small>
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @if ($staySrc = ProtectedMedia::src($day['stay_image'] ?? null))
                                                    <figure class="tm-day-photo tm-day-stay-photo" data-protected-media>
                                                        <img src="{{ $staySrc }}" alt="" loading="lazy" decoding="async" draggable="false">
                                                        @if (filled($day['stay_caption'] ?? null))
                                                            <figcaption>{{ $day['stay_caption'] }}</figcaption>
                                                        @endif
                                                    </figure>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Price & booking card --}}
                <aside class="tm-booking-card" aria-label="Price and booking">
                    @if ($package['price'] ?? null)
                        <p class="tm-booking-from">From</p>
                        <p class="tm-booking-price">{{ $currency }}{{ number_format($package['price']) }}</p>
                        <p class="tm-booking-note">per person sharing, excluding international flights</p>
                    @else
                        <p class="tm-booking-from">Price</p>
                        <p class="tm-booking-price tm-booking-price-sm">On request</p>
                        <p class="tm-booking-note">tailored to your group size, dates and comfort level</p>
                    @endif

                    @if (! empty($package['facts']))
                        <dl class="tm-booking-facts">
                            @foreach ($package['facts'] as $label => $value)
                                <div>
                                    <dt>{{ $label }}</dt>
                                    <dd>{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif

                    <a href="{{ $bookUrl }}" class="tm-booking-primary">
                        Book This Adventure
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                    </a>
                    @if ($canQuote)
                        <button type="button" class="tm-booking-secondary" data-dialog-open="instant-quote">
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.62a1.5 1.5 0 0 0-.44-1.06l-4.12-4.12A1.5 1.5 0 0 0 11.38 2H4.5Zm6.25 6.75a.75.75 0 0 0-1.5 0v2.69l-.97-.97a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l2.25-2.25a.75.75 0 1 0-1.06-1.06l-.97.97V8.75Z" clip-rule="evenodd"/></svg>
                            Get an Instant Quote
                        </button>
                    @endif
                    <a href="{{ $askUrl }}" class="tm-booking-secondary" @if ($whatsapp) target="_blank" rel="noopener" @endif>Ask a Question</a>
                    <p class="tm-booking-small">No payment taken online. We confirm availability first.</p>
                </aside>
            </div>
        </div>
    </section>

    {{-- Instant quote: short form -> PDF quotation (QuoteController) --}}
    @if ($canQuote)
        <dialog id="instant-quote" class="tm-review-dialog tm-quote-dialog" aria-labelledby="instant-quote-title"
                @if ($instantQuote || $quoteErrors->any()) data-open-on-load @endif>
            <button type="button" class="tm-review-dialog-close" data-dialog-close aria-label="Close">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/></svg>
            </button>

            @if ($instantQuote)
                <div class="tm-form-success" role="status">
                    <span class="tm-feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L19 7"/></svg>
                    </span>
                    <h2 id="instant-quote-title" class="tm-form-title">Your quote is ready{{ ! empty($instantQuote['name']) ? ', '.$instantQuote['name'] : '' }}!</h2>
                    <p>Reference <strong class="notranslate" translate="no">{{ $instantQuote['reference'] }}</strong>. Download the PDF below; our team has a copy and will be in touch to confirm availability.</p>
                    <a href="{{ $instantQuote['url'] }}" class="tm-btn-primary tm-form-submit" data-quote-download>
                        Download Quote (PDF)
                    </a>
                    <p class="tm-quote-small">The download link works for 7 days.</p>
                </div>
            @else
                <p class="tm-quote-kicker">Instant Quote</p>
                <h2 id="instant-quote-title" class="tm-form-title">{{ $package['title'] }}</h2>
                <p class="tm-form-intro">Tell us when and who is travelling and we will create a PDF quotation for you straight away. No payment, no obligation.</p>

                @if ($quoteErrors->any())
                    <div class="tm-form-alert" role="alert">Please check the highlighted fields below.</div>
                @endif

                <form method="POST" action="{{ route('quotes.store', $package['slug']) }}" class="tm-form" novalidate data-quote-form>
                    @csrf

                    {{-- Honeypot: hidden from people, filled by bots. --}}
                    <div class="tm-newsletter-hp" aria-hidden="true">
                        <label for="quote-website">Website</label>
                        <input type="text" id="quote-website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="tm-form-row">
                        <x-form-field name="name" label="Full name" required autocomplete="name" :bag="$quoteErrors" />
                        <x-form-field name="email" type="email" label="Email" required autocomplete="email" :bag="$quoteErrors" />
                    </div>
                    <div class="tm-form-row">
                        <x-form-field name="phone" type="tel" label="Phone / WhatsApp" autocomplete="tel" :bag="$quoteErrors" />
                        <x-form-field name="country" label="Country of residence" autocomplete="country-name" :bag="$quoteErrors" />
                    </div>
                    <div class="tm-form-row tm-form-row-3">
                        <x-form-field name="travel_date" type="date" label="Travel date" required :min="now()->toDateString()" :bag="$quoteErrors" />
                        <x-form-field name="adults" type="number" label="Adults" required min="1" max="30" :value="old('adults', 2)" :bag="$quoteErrors" />
                        <x-form-field name="children" type="number" label="Children" min="0" max="30" :value="old('children', 0)" :bag="$quoteErrors" />
                    </div>

                    <button type="submit" class="tm-btn-primary tm-form-submit">Create My Quote</button>
                    <p class="tm-quote-small">Based on {{ $currency }}{{ number_format($package['price']) }} per person. Your details are only used to prepare and follow up this quote.</p>
                </form>
            @endif
        </dialog>
    @endif

    {{-- What's included --}}
    @if (! empty($package['included']) || ! empty($package['excluded']))
        <section class="bg-tm-cream py-14 sm:py-20" aria-labelledby="included-title">
            <div class="tm-container">
                <h2 id="included-title" class="tm-package-heading text-center">What's Included</h2>
                <div class="tm-included">
                    @if (! empty($package['included']))
                        <div>
                            <h3 class="tm-included-title">Included</h3>
                            <ul>
                                @foreach ($package['included'] as $item)
                                    <li class="is-yes">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (! empty($package['excluded']))
                        <div>
                            <h3 class="tm-included-title is-muted">Not Included</h3>
                            <ul>
                                @foreach ($package['excluded'] as $item)
                                    <li class="is-no">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Reviews of this trip --}}
    @if ($reviews->isNotEmpty())
        <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="trip-reviews-title">
            <div class="tm-container">
                <div class="tm-trip-reviews-head">
                    <div>
                        <h2 id="trip-reviews-title" class="tm-package-heading">Reviews of This Trip</h2>
                        @php($average = round($reviews->avg(fn ($r) => $r['rating'] ?? 5), 1))
                        <p class="tm-trip-reviews-score">
                            <x-review-stars :rating="$average" />
                            {{ number_format($average, 1) }} · {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
                        </p>
                    </div>
                    <a href="{{ route('reviews') }}" class="tm-review-more">All reviews →</a>
                </div>
                <div class="tm-card-grid is-start mt-8">
                    @foreach ($reviews as $i => $review)
                        <x-review-card :review="$review" :id="'trip-review-'.$i" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- More trips --}}
    @if ($related->isNotEmpty())
        <section class="bg-tm-cream py-14 sm:py-20" aria-labelledby="related-packages-title">
            <div class="tm-container">
                <p class="tm-section-kicker">Keep Exploring</p>
                <h2 id="related-packages-title" class="tm-section-title">You Might Also Like</h2>
                <div class="tm-divider" aria-hidden="true"></div>
                <div class="tm-card-grid mt-10">
                    @foreach ($related as $item)
                        <x-package-card :package="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
