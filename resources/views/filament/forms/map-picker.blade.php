{{--
    Map picker for one itinerary day (Packages → Itinerary). Click the map or drag the pin
    to set where the day is pinned on the route map; this fills in the day's coordinates
    and switches "Map location" to "Other place". Other days show as faint numbered dots.
--}}
@php
    use App\Support\RouteMap;
    use Illuminate\Support\Str;

    $statePath = $getStatePath();
    $base = Str::beforeLast($statePath, '.');           // this day, e.g. data.itinerary.{uuid}
    $itineraryPath = Str::beforeLast($base, '.');       // data.itinerary
    $dayKey = Str::afterLast($base, '.');

    $days = (array) data_get($getLivewire(), $itineraryPath, []);
    $current = $days[$dayKey] ?? [];
    $point = RouteMap::placeFor((array) $current);

    $others = [];
    $number = 0;
    foreach ($days as $key => $day) {
        $number++;
        if ((string) $key === (string) $dayKey) {
            $dayNumber = $number;
            continue;
        }
        if ($place = RouteMap::placeFor((array) $day)) {
            $others[] = ['day' => $number, 'lat' => $place['lat'], 'lng' => $place['lng'], 'name' => $place['name']];
        }
    }

    $mapKey = 'map-picker-'.md5($base.json_encode($point).json_encode($others));
@endphp

<div wire:key="{{ $mapKey }}"
     x-data="shanyangiMapPicker({
        base: @js($base),
        point: @js($point),
        others: @js($others),
        dayNumber: @js($dayNumber ?? null),
        leafletJs: @js(asset('vendor/leaflet/leaflet.js')),
        leafletCss: @js(asset('vendor/leaflet/leaflet.css')),
     })"
     class="sa-map-picker">
    <div class="sa-map-picker-map" x-ref="map" wire:ignore></div>
    <p class="sa-map-picker-hint">
        <strong>Click the map</strong> or <strong>drag the pin</strong> to set where Day {{ $dayNumber ?? '' }} appears on the route map.
        @if (! $point)
            No place is set yet for this day.
        @endif
    </p>
</div>
