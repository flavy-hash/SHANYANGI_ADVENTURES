@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' ' : '').'Safaris & Tours')
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

            {{-- Filter panel: one choice per row; rows combine (PageController@safaris) --}}
            <div class="tm-filter-panel" id="filters" role="search" aria-label="Filter packages">
                @foreach ($filterRows as $row)
                    <div class="tm-filter-row">
                        <p class="tm-filter-row-label" id="filter-{{ Str::slug($row['label']) }}">{{ $row['label'] }}</p>
                        <ul class="tm-filter-row-chips" aria-labelledby="filter-{{ Str::slug($row['label']) }}">
                            @foreach ($row['chips'] as $chip)
                                <li>
                                    <a href="{{ $chip['url'] }}" @class(['tm-filter-chip', 'is-active' => $chip['active']])
                                       @if ($chip['active']) aria-current="true" @endif rel="nofollow">{{ $chip['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                {{-- Price slider: a maximum price per person. Submits on release (resources/js/site.js);
                     the other active filters ride along as hidden fields. --}}
                @if ($priceFilter)
                    @php
                        $span = max(1, $priceFilter['max'] - $priceFilter['min']);
                        $pct = round(($priceFilter['value'] - $priceFilter['min']) / $span * 100, 2);
                        $money = fn (int $amount) => $priceFilter['currency'].number_format($amount);
                    @endphp
                    <form class="tm-filter-row" method="get" action="{{ route('safaris') }}#filters" data-price-filter>
                        @foreach ($priceFilter['keep'] as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        <label class="tm-filter-row-label" for="price-max">Tour price <span class="tm-price-label-note">(per person)</span></label>
                        <div class="tm-price-range" style="--pct: {{ $pct }}%">
                            <output class="tm-price-bubble" for="price-max" data-price-bubble>{{ $money($priceFilter['value']) }}</output>
                            <input type="range" id="price-max" name="price_max" class="tm-price-input"
                                   min="{{ $priceFilter['min'] }}" max="{{ $priceFilter['max'] }}" step="{{ $priceFilter['step'] }}" value="{{ $priceFilter['value'] }}"
                                   data-currency="{{ $priceFilter['currency'] }}" aria-valuetext="Up to {{ $money($priceFilter['value']) }} per person">
                            <div class="tm-price-ends" aria-hidden="true">
                                <span>{{ $money($priceFilter['min']) }}</span>
                                <span>{{ $money($priceFilter['max']) }}</span>
                            </div>
                            <noscript><button type="submit" class="tm-filter-chip">Apply price</button></noscript>
                        </div>
                    </form>
                @endif

                <div class="tm-filter-summary">
                    <p><strong>{{ $packages->count() }}</strong> of {{ $total }} {{ Str::plural('trip', $total) }}</p>
                    @if ($activePrice)
                        <a href="{{ $activePrice['remove'] }}#filters" class="tm-filter-tag">{{ $activePrice['label'] }} <span aria-label="remove">×</span></a>
                    @endif
                    @if ($activeType)
                        <a href="{{ $activeType['remove'] }}" class="tm-filter-tag">{{ $activeType['label'] }} <span aria-label="remove">×</span></a>
                    @endif
                    @if ($hasFilters)
                        <a href="{{ route('safaris') }}" class="tm-filter-clear">Clear filters</a>
                    @endif
                </div>
            </div>

            @if ($packages->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($packages as $package)
                        <x-package-card :package="$package" />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">No packages match these filters. <a href="{{ route('safaris') }}">Show all packages</a>, or <a href="{{ route('contact') }}#request">tell us what you have in mind</a> and we will plan it for you.</p>
            @endif
        </div>
    </section>

    <x-cta-band
        title="Don't see the trip you want?"
        text="Every itinerary can be changed. Tell us your dates, group and interests, and we will design a private safari around you."
    />
@endsection
