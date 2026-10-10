@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Accommodation | Shanyangi Adventures')
@section('description', 'Safari lodges, tented camps and beach stays in the Serengeti, Ngorongoro, Karatu and Zanzibar.')

@section('content')
    <x-page-hero
        title="Where You'll Stay"
        kicker="Lodges, Camps & Beach Stays"
        subtitle="Hand-picked places to rest between adventures, from canvas under the stars to villas by the ocean."
        image="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=2000&q=75"
        :crumbs="array_filter([['label' => 'Accommodation', 'url' => route('accommodations')], $activeLabel ? ['label' => $activeLabel] : null])"
    />

    <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="stays-list-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Accommodation</p>
            <h2 id="stays-list-title" class="tm-section-title">{{ $activeLabel ? 'Stays in '.$activeLabel : 'Stays for Every Route' }}</h2>
            <div class="tm-divider" aria-hidden="true"></div>
            <p class="tm-section-subtitle">
                We match lodges and camps to your route, comfort level and budget. These are some of the places we love to use.
            </p>

            <x-filter-chips :filters="$filters" label="Filter stays by location" />

            @if ($stays->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($stays as $stay)
                        <x-info-card
                            :title="$stay['name']"
                            :href="route('contact', ['trip' => 'Stay: '.$stay['name']]).'#request'"
                            :image="$stay['image']"
                            :pill="$locations[$stay['location']] ?? null"
                            :chips="[$stay['type'], $stay['level']]"
                            :text="$stay['text']"
                            link-label="Enquire"
                        />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">No stays listed here yet. <a href="{{ route('contact') }}#request">Ask us</a> for options in this area.</p>
            @endif
        </div>
    </section>

    <x-cta-band
        title="Need help choosing where to stay?"
        text="Tell us your route and comfort level, and we will recommend lodges and camps that fit your budget."
    />
@endsection
