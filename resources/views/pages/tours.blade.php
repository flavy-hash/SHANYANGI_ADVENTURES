@extends('layouts.app')

@use('App\Support\ProtectedMedia')

@section('title', 'Tanzania Tours & Safari Packages')
@section('description', 'Browse every Shanyangi trip: Tanzania safaris, Kilimanjaro climbs, honeymoons, family trips and Zanzibar escapes. Filter by trip type and length.')

@php
    $currency = config('packages.currency', '$');
    $activeCount = count($selectedTypes) + count($selectedLengths);

    // Link that removes one filter value but keeps the rest.
    $without = function (string $param, string $value) use ($selectedTypes, $selectedLengths, $sort) {
        $query = array_filter([
            'type' => array_values(array_diff($selectedTypes, $param === 'type' ? [$value] : [])),
            'length' => array_values(array_diff($selectedLengths, $param === 'length' ? [$value] : [])),
            'sort' => $sort !== 'recommended' ? $sort : null,
        ]);

        return route('tours', $query);
    };
@endphp

@section('content')
    <x-page-hero
        title="Tanzania Tours"
        kicker="All Our Trips"
        subtitle="Safaris on the northern circuit, Kilimanjaro climbs and slow days on Zanzibar's beaches. Choose a trip as it is, or let us shape it around you."
        image="https://images.unsplash.com/photo-1535941339077-2dd1c7963098?auto=format&fit=crop&w=2000&q=75"
        :crumbs="[['label' => 'Tanzania Tours']]"
    />

    <section class="bg-tm-beige py-12 sm:py-16" aria-labelledby="tours-title">
        <div class="tm-container">
            <h2 id="tours-title" class="sr-only">Find a tour</h2>

            <form method="GET" action="{{ route('tours') }}" class="tm-tours" data-tours-form>
                {{-- Filters --}}
                <aside class="tm-tours-filters" aria-label="Filter trips" data-tours-filters>
                    <button type="button" class="tm-tours-filters-toggle" aria-expanded="false" data-tours-filters-toggle>
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4.75A.75.75 0 0 1 3.75 4h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 4.75Zm3 5A.75.75 0 0 1 6.75 9h6.5a.75.75 0 0 1 0 1.5h-6.5A.75.75 0 0 1 6 9.75Zm2.5 5a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Z"/></svg>
                        Filter trips
                        @if ($activeCount)<span class="tm-tours-filters-count">{{ $activeCount }}</span>@endif
                    </button>

                    <div class="tm-tours-filters-body" data-tours-filters-body>
                        <fieldset class="tm-tours-group">
                            <legend>Trip type</legend>
                            @foreach ($types as $key => $label)
                                <label class="tm-tours-option">
                                    <input type="checkbox" name="type[]" value="{{ $key }}" @checked(in_array($key, $selectedTypes, true))>
                                    <span>{{ $label }}</span>
                                    <small>{{ $typeCounts[$key] ?? 0 }}</small>
                                </label>
                            @endforeach
                        </fieldset>

                        <fieldset class="tm-tours-group">
                            <legend>Trip length</legend>
                            @foreach ($lengths as $key => $label)
                                <label class="tm-tours-option">
                                    <input type="checkbox" name="length[]" value="{{ $key }}" @checked(in_array($key, $selectedLengths, true))>
                                    <span>{{ $label }}</span>
                                    <small>{{ $lengthCounts[$key] ?? 0 }}</small>
                                </label>
                            @endforeach
                        </fieldset>

                        <noscript><button type="submit" class="tm-btn-primary w-full">Show trips</button></noscript>

                        <div class="tm-tours-help">
                            <p><strong>Can't find your trip?</strong> Every itinerary can be changed, or we plan one from scratch.</p>
                            <a href="{{ route('contact') }}#request" class="tm-text-link">Plan a custom trip →</a>
                        </div>
                    </div>
                </aside>

                {{-- Results --}}
                <div class="tm-tours-results">
                    <div class="tm-tours-bar">
                        <p class="tm-tours-count" aria-live="polite">
                            <strong>{{ $tours->count() }}</strong> {{ Str::plural('trip', $tours->count()) }}@if ($activeCount) <span>of {{ $total }}</span>@endif
                        </p>

                        <label class="tm-tours-sort">
                            <span>Sort by</span>
                            <select name="sort" class="tm-input">
                                @foreach ($sorts as $key => $label)
                                    <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    @if ($activeCount)
                        <ul class="tm-tours-active" aria-label="Active filters">
                            @foreach ($selectedTypes as $key)
                                <li><a href="{{ $without('type', $key) }}">{{ $types[$key] }} <span aria-label="remove">×</span></a></li>
                            @endforeach
                            @foreach ($selectedLengths as $key)
                                <li><a href="{{ $without('length', $key) }}">{{ $lengths[$key] }} <span aria-label="remove">×</span></a></li>
                            @endforeach
                            <li><a href="{{ route('tours') }}" class="is-clear">Clear all</a></li>
                        </ul>
                    @endif

                    <p class="tm-tours-note">
                        <strong>Every trip can be tailored.</strong> These are our most-loved routes; we happily change the days, parks, lodges and pace to suit you.
                    </p>

                    @forelse ($tours as $tour)
                        @php
                            $route = $tour->route_summary;
                            $shown = array_slice($route, 0, 5);
                            $badge = match (true) {
                                $tour->is_featured => 'Popular',
                                in_array('honeymoon', $tour->types ?? [], true) => 'For couples',
                                in_array('family', $tour->types ?? [], true) => 'Family-friendly',
                                default => null,
                            };
                            $url = route('safaris.show', $tour->slug);
                        @endphp
                        <article class="tm-tour">
                            <a href="{{ $url }}" class="tm-tour-media" tabindex="-1" aria-hidden="true" data-protected-media>
                                @if ($src = ProtectedMedia::src($tour->image, 800))
                                    <img src="{{ $src }}" alt="" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async" draggable="false">
                                @endif
                                @if ($badge)
                                    <span class="tm-tour-badge">{{ $badge }}</span>
                                @endif
                                @if ($tour->category)
                                    <span class="tm-tour-category">{{ $tour->category }}</span>
                                @endif
                            </a>

                            <div class="tm-tour-body">
                                <div class="tm-tour-price">
                                    @if ($tour->price)
                                        <span>From</span>
                                        <strong>{{ $currency }}{{ number_format($tour->price) }}</strong>
                                        <small>USD</small>
                                    @else
                                        <span>Price</span>
                                        <strong class="is-small">On request</strong>
                                    @endif
                                </div>

                                <h3 class="tm-tour-title"><a href="{{ $url }}">{{ $tour->title }}</a></h3>
                                <p class="tm-tour-meta">
                                    @if ($tour->days){{ $tour->days }} {{ Str::plural('day', $tour->days) }}@endif
                                    @foreach (array_slice($tour->types ?? [], 0, 3) as $type)
                                        @if (isset($types[$type])) · {{ $types[$type] }}@endif
                                    @endforeach
                                </p>

                                @if ($tour->highlights)
                                    <ul class="tm-tour-highlights">
                                        @foreach (array_slice($tour->highlights, 0, 3) as $highlight)
                                            <li>{{ $highlight }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($route)
                                    <ol class="tm-tour-route" aria-label="Route">
                                        @foreach ($shown as $stop)
                                            <li>{{ $stop }}</li>
                                        @endforeach
                                        @if (count($route) > count($shown))
                                            <li class="is-more">+{{ count($route) - count($shown) }} more {{ Str::plural('stop', count($route) - count($shown)) }}</li>
                                        @endif
                                    </ol>
                                @endif

                                <a href="{{ $url }}" class="tm-tour-cta">View this trip</a>
                            </div>
                        </article>
                    @empty
                        <div class="tm-tours-empty">
                            <p>No trips match these filters.</p>
                            <a href="{{ route('tours') }}" class="tm-btn-primary">Show all trips</a>
                            <a href="{{ route('contact') }}#request" class="tm-text-link">or ask us to plan one →</a>
                        </div>
                    @endforelse
                </div>
            </form>
        </div>
    </section>

    <x-cta-band title="Not sure which trip suits you?" text="Tell us your dates, interests and budget, and we will suggest the right route." />
@endsection
