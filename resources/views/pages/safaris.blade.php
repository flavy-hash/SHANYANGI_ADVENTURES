@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Safaris & Tours | Shanyangi Adventures')
@section('description', 'Tanzania safari packages, Kilimanjaro climbs and Zanzibar escapes, tailored to your dates, interests and budget.')

@section('content')
    <x-page-hero
        title="Safaris & Tours"
        kicker="Explore Tanzania"
        subtitle="Wildlife safaris, Kilimanjaro climbs and Zanzibar escapes. Book a journey as it is, or let us tailor it to you."
        image="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=2000&q=75"
        :crumbs="array_filter([['label' => 'Safaris', 'url' => route('safaris')], $activeLabel ? ['label' => $activeLabel] : null])"
    />

    <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="packages-list-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Our Packages</p>
            <h2 id="packages-list-title" class="tm-section-title">{{ $activeLabel ? $activeLabel.' Journeys' : 'Find Your Perfect Journey' }}</h2>
            <div class="tm-divider" aria-hidden="true"></div>

            <x-filter-chips :filters="$filters" label="Filter packages" />

            @if ($packages->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($packages as $package)
                        <x-package-card :package="$package" />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">No packages in this category yet. <a href="{{ route('contact') }}#request">Tell us what you have in mind</a> and we will plan it for you.</p>
            @endif
        </div>
    </section>

    <x-cta-band
        title="Don't see the trip you want?"
        text="Every itinerary can be changed. Tell us your dates, group and interests, and we will design a private safari around you."
    />
@endsection
