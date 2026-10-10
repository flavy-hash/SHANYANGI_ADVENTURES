@extends('layouts.app')

@section('title', 'Contact Us & Plan Your Trip | Shanyangi Adventures')
@section('description', 'Send a trip request or get in touch with Shanyangi Adventures to start planning your Tanzania safari.')

@php
    $whatsapp = preg_replace('/\D/', '', (string) config('site.whatsapp'));
    $phone = preg_replace('/[^\d+]/', '', (string) config('site.phone'));
    $email = config('site.email');
    $address = config('site.address');
    $formErrors = $errors->getBag('tripRequest');
    $sent = session('trip_request_sent');
@endphp

@section('content')
    <x-page-hero
        title="Plan Your Trip"
        kicker="Contact Us"
        subtitle="Tell us about the journey you have in mind. We will reply with ideas, an itinerary and a quote."
        image="https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=2000&q=75"
        :crumbs="[['label' => 'Contact Us']]"
    />

    <section class="bg-tm-beige py-14 sm:py-20">
        <div class="tm-container">
            <div class="tm-contact-grid">
                {{-- Trip request form --}}
                <div id="request" class="tm-form-card" aria-labelledby="request-title">
                    @if ($sent)
                        <div class="tm-form-success" role="status">
                            <span class="tm-feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L19 7"/></svg>
                            </span>
                            <h2 id="request-title" class="tm-form-title">Thank you{{ is_string($sent) ? ', '.$sent : '' }}!</h2>
                            <p>Your trip request has been sent. We will get back to you by email as soon as possible.</p>
                            <a href="{{ route('safaris') }}" class="tm-text-link">
                                Browse more journeys
                                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                            </a>
                        </div>
                    @else
                        <h2 id="request-title" class="tm-form-title">Send a Trip Request</h2>
                        <p class="tm-form-intro">Fields marked <span class="tm-required">*</span> are required. Everything else helps us plan, but you can leave it blank.</p>

                        @if ($formErrors->any())
                            <div class="tm-form-alert" role="alert">Please check the highlighted fields below.</div>
                        @endif

                        <form method="POST" action="{{ route('trip-request.store') }}" class="tm-form" novalidate>
                            @csrf

                            {{-- Honeypot: hidden from people, filled by bots. --}}
                            <div class="tm-newsletter-hp" aria-hidden="true">
                                <label for="trip-website">Website</label>
                                <input type="text" id="trip-website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="tm-form-row">
                                <x-form-field name="name" label="Full name" required autocomplete="name" :bag="$formErrors" />
                                <x-form-field name="email" type="email" label="Email" required autocomplete="email" :bag="$formErrors" />
                            </div>

                            <div class="tm-form-row">
                                <x-form-field name="phone" type="tel" label="Phone / WhatsApp" autocomplete="tel" :bag="$formErrors" />
                                <x-form-field name="country" label="Country of residence" autocomplete="country-name" :bag="$formErrors" />
                            </div>

                            <x-form-field name="trip" label="Trip of interest" :value="$trip" placeholder="e.g. Serengeti & Ngorongoro Safari" :bag="$formErrors" />

                            <fieldset class="tm-field">
                                <legend class="tm-label">What are you interested in?</legend>
                                <div class="tm-checks">
                                    @foreach ($interests as $key => $label)
                                        <label class="tm-check">
                                            <input type="checkbox" name="interests[]" value="{{ $key }}" @checked(in_array($key, old('interests', []), true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <div class="tm-form-row tm-form-row-4">
                                <x-form-field name="travel_date" type="date" label="Travel date" :min="now()->toDateString()" :bag="$formErrors" />
                                <x-form-field name="duration_days" type="number" label="Days" min="1" max="60" placeholder="e.g. 7" :bag="$formErrors" />
                                <x-form-field name="adults" type="number" label="Adults" required min="1" max="50" :value="old('adults', 2)" :bag="$formErrors" />
                                <x-form-field name="children" type="number" label="Children" min="0" max="50" :value="old('children', 0)" :bag="$formErrors" />
                            </div>

                            <div class="tm-field">
                                <label for="budget" class="tm-label">Budget per person</label>
                                <select id="budget" name="budget" @class(['tm-input', 'is-invalid' => $formErrors->has('budget')])>
                                    <option value="">Select a range</option>
                                    @foreach ($budgets as $key => $label)
                                        <option value="{{ $key }}" @selected(old('budget') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @if ($formErrors->has('budget')) <p class="tm-field-error">{{ $formErrors->first('budget') }}</p> @endif
                            </div>

                            <div class="tm-field">
                                <label for="message" class="tm-label">Tell us about your trip</label>
                                <textarea id="message" name="message" rows="5" maxlength="3000" @class(['tm-input', 'is-invalid' => $formErrors->has('message')])
                                          placeholder="Who is travelling, what you would love to see, special occasions...">{{ old('message') }}</textarea>
                                @if ($formErrors->has('message')) <p class="tm-field-error">{{ $formErrors->first('message') }}</p> @endif
                            </div>

                            <button type="submit" class="tm-btn-primary tm-form-submit">Send Trip Request</button>
                        </form>
                    @endif
                </div>

                {{-- Contact details --}}
                <aside class="tm-contact-aside" aria-labelledby="contact-details-title">
                    <h2 id="contact-details-title" class="tm-form-title">Get in Touch</h2>
                    <p class="tm-form-intro">Prefer to talk first? Reach us directly.</p>

                    @if ($whatsapp || $phone || $email || $address)
                        <ul class="tm-contact-list">
                            @if ($whatsapp)
                                <li>
                                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="tm-contact-item">
                                        <span class="tm-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a8.5 8.5 0 0 1-12.5 7.5L3 20l1.1-4.3A8.5 8.5 0 1 1 20 11.5Z"/></svg></span>
                                        <span><strong>WhatsApp</strong><small>Chat with our team</small></span>
                                    </a>
                                </li>
                            @endif
                            @if ($phone)
                                <li>
                                    <a href="tel:{{ $phone }}" class="tm-contact-item">
                                        <span class="tm-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h1.38a1.5 1.5 0 0 1 1.41 1l.81 2.86a1.5 1.5 0 0 1-.46 1.75l-.9.72a11 11 0 0 0 5.23 5.23l.72-.9a1.5 1.5 0 0 1 1.75-.46l2.86.81a1.5 1.5 0 0 1 1 1.41v1.48a1.5 1.5 0 0 1-1.5 1.5h-.38A13.42 13.42 0 0 1 4 5.98v-.48Z"/></svg></span>
                                        <span><strong>Call us</strong><small>{{ config('site.phone') }}</small></span>
                                    </a>
                                </li>
                            @endif
                            @if ($email)
                                <li>
                                    <a href="mailto:{{ $email }}" class="tm-contact-item">
                                        <span class="tm-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                                        <span><strong>Email</strong><small>{{ $email }}</small></span>
                                    </a>
                                </li>
                            @endif
                            @if ($address)
                                <li>
                                    <div class="tm-contact-item">
                                        <span class="tm-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 1 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                                        <span><strong>Office</strong><small>{{ $address }}</small></span>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="tm-contact-note">Use the form and we will reply by email.</p>
                    @endif

                    @if (\App\Support\SocialLinks::active())
                        <div class="tm-contact-follow">
                            <strong>Follow us</strong>
                            <x-social-links class="tm-social-light" />
                        </div>
                    @endif

                    <div class="tm-contact-hint">
                        <strong>What happens next?</strong>
                        <ol>
                            <li>We read your request and may ask a few questions.</li>
                            <li>We send you a suggested itinerary and quote.</li>
                            <li>We adjust it together until it is just right.</li>
                        </ol>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
