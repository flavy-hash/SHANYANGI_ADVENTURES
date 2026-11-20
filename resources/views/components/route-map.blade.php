@props(['package'])

@php
    $stops = \App\Support\RouteMap::stops($package);
    $tiles = array_filter([
        'mapbox' => config('maps.mapbox_token'),
        'style' => config('maps.mapbox_style'),
    ]);
@endphp

{{-- Itinerary route map: pins per stop ("Day 3-4"), joined in order (resources/js/route-map.js). --}}
@if (count($stops) >= 2)
    <section class="tm-route-map-card" aria-labelledby="route-map-title">
        <h3 id="route-map-title" class="tm-package-subheading">Route Map</h3>

        <div class="tm-route-map" data-route-map
             data-stops="{{ json_encode($stops) }}"
             data-tiles="{{ json_encode((object) $tiles) }}"
             role="img" aria-label="Map of the route: {{ collect($stops)->pluck('name')->implode(', then ') }}">
        </div>

        {{-- The route in words (also shown if the map can't load). --}}
        <ol class="tm-route-stops">
            @foreach ($stops as $stop)
                <li>
                    <span class="tm-route-stop-day">{{ $stop['label'] }}</span>
                    <span class="tm-route-stop-name">{{ $stop['name'] }}</span>
                </li>
            @endforeach
        </ol>
    </section>
@endif
