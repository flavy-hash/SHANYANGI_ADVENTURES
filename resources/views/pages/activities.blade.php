@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Activities & Experiences | Shanyangi Adventures')
@section('description', 'Day trips and experiences across Tanzania: walking safaris, waterfalls, cultural tours, balloon flights and Zanzibar excursions.')

@section('content')
    <x-page-hero
        title="Activities & Experiences"
        kicker="Beyond the Game Drive"
        subtitle="Walking safaris, waterfalls, village visits, balloon flights and island days to add to any journey."
        image="https://images.unsplash.com/photo-1507608616759-54f48f0af0ee?auto=format&fit=crop&w=2000&q=75"
        :crumbs="array_filter([['label' => 'Activities', 'url' => route('activities')], $activeLabel ? ['label' => $activeLabel] : null])"
    />

    <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="activities-list-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Things to Do</p>
            <h2 id="activities-list-title" class="tm-section-title">{{ $activeLabel ? 'Experiences in '.$activeLabel : 'Experiences Across Tanzania' }}</h2>
            <div class="tm-divider" aria-hidden="true"></div>

            <x-filter-chips :filters="$filters" label="Filter activities by location" />

            @if ($activities->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($activities as $activity)
                        <x-info-card
                            :title="$activity['title']"
                            :href="route('contact', ['trip' => $activity['title']]).'#request'"
                            :image="$activity['image']"
                            :pill="$locations[$activity['location']] ?? null"
                            :chips="[$activity['duration']]"
                            :text="$activity['description']"
                            link-label="Enquire"
                        />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">No activities listed here yet. <a href="{{ route('contact') }}#request">Ask us</a> about experiences in this area.</p>
            @endif
        </div>
    </section>

    <x-cta-band
        title="Add experiences to your safari"
        text="Most activities fit easily around game drives. Tell us what interests you and we will build them into your itinerary."
    />
@endsection
