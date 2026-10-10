@extends('layouts.app')

@section('title', 'About Us | Shanyangi Adventures')
@section('description', 'Meet Shanyangi Adventures, a locally owned Tanzanian tour company planning private safaris, climbs and beach escapes.')

@use('App\Support\AboutPage')

@php
    // Content is edited in the admin (About Us page); see App\Support\AboutPage for defaults.
    $icons = AboutPage::icons();
    // Keep the brand name untranslated wherever it appears in the editable text.
    $brand = fn (string $text) => str_replace(e('Shanyangi Adventures'), '<span class="notranslate" translate="no">Shanyangi Adventures</span>', e($text));
@endphp

@section('content')
    <x-page-hero
        :title="$about['hero_title']"
        kicker="Who We Are"
        :subtitle="$about['hero_subtitle']"
        :image="$about['hero_image'] ?: 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&w=2000&q=75'"
        :crumbs="[['label' => 'About Us']]"
    />

    {{-- Our story --}}
    <section id="story" class="bg-tm-beige py-16 sm:py-24" aria-labelledby="story-title">
        <div class="tm-container">
            <div class="tm-split">
                <div>
                    <p class="tm-section-kicker tm-align-start">Our Story</p>
                    <h2 id="story-title" class="tm-section-title tm-align-start">{{ $about['story_title'] }}</h2>
                    <div class="tm-divider tm-align-start" aria-hidden="true"></div>
                    <div class="tm-split-text">
                        @foreach (AboutPage::paragraphs($about['story']) as $paragraph)
                            <p>{!! $brand($paragraph) !!}</p>
                        @endforeach
                    </div>
                </div>

                <div class="tm-split-media" data-protected-media>
                    <img src="{{ \App\Support\ProtectedMedia::src($about['story_image'] ?: 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1000&q=75') }}" alt="" loading="lazy" decoding="async" draggable="false">
                </div>
            </div>
        </div>
    </section>

    @include('partials.why-choose-us')

    {{-- Team --}}
    <section id="team" class="bg-tm-beige py-16 sm:py-24" aria-labelledby="team-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Our Team</p>
            <h2 id="team-title" class="tm-section-title">{{ $about['team_title'] }}</h2>
            <div class="tm-divider" aria-hidden="true"></div>
            @if ($about['team_subtitle'])
                <p class="tm-section-subtitle">{{ $about['team_subtitle'] }}</p>
            @endif

            <ul class="mx-auto mt-12 grid max-w-6xl gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($about['team'] as $member)
                    <li class="tm-feature-card bg-white">
                        <span class="tm-feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$member['icon'] ?? ''] ?? $icons['users'] !!}</svg>
                        </span>
                        <h3 class="tm-feature-title">{{ $member['title'] }}</h3>
                        <p class="tm-feature-text">{{ $member['text'] ?? '' }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    @include('partials.reviews')

    <x-cta-band title="Let's plan your trip together" />
@endsection
