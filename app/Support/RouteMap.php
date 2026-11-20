<?php

namespace App\Support;

use App\Models\Package;

/**
 * Stops for the itinerary route map on a package page.
 *
 * Each day is pinned at a place from config/places.php: the one chosen in the admin,
 * a custom point, or (on "Automatic") the place named in the day's title, photo
 * caption or accommodation. Consecutive days at the same place share one pin
 * ("Day 3-4"), like a real route.
 */
class RouteMap
{
    public const AUTO = 'auto';

    public const CUSTOM = 'custom';

    public const NONE = 'none';

    /**
     * @return list<array{label: string, name: string, lat: float, lng: float, days: list<int>}>
     */
    public static function stops(Package $package): array
    {
        $stops = [];

        foreach (array_values($package->itinerary ?? []) as $index => $day) {
            $place = self::placeFor((array) $day);
            if (! $place) {
                continue;
            }

            $dayNumber = $index + 1;
            $last = $stops ? $stops[array_key_last($stops)] : null;

            // Same place as the previous day (and no gap): extend that pin.
            if ($last && $last['name'] === $place['name'] && end($last['days']) === $dayNumber - 1) {
                $stops[array_key_last($stops)]['days'][] = $dayNumber;
            } else {
                $stops[] = ['name' => $place['name'], 'lat' => $place['lat'], 'lng' => $place['lng'], 'days' => [$dayNumber]];
            }
        }

        return array_map(function (array $stop) {
            $first = $stop['days'][0];
            $last = end($stop['days']);

            return ['label' => $first === $last ? "Day {$first}" : "Day {$first}-{$last}"] + $stop;
        }, $stops);
    }

    /**
     * @return array{name: string, lat: float, lng: float}|null
     */
    public static function placeFor(array $day): ?array
    {
        $choice = (string) ($day['map_place'] ?? '') ?: self::AUTO;

        if ($choice === self::NONE) {
            return null;
        }

        if ($choice === self::CUSTOM) {
            $lat = filter_var($day['map_lat'] ?? null, FILTER_VALIDATE_FLOAT);
            $lng = filter_var($day['map_lng'] ?? null, FILTER_VALIDATE_FLOAT);

            return $lat !== false && $lng !== false && abs($lat) <= 90 && abs($lng) <= 180
                ? ['name' => trim((string) ($day['map_name'] ?? '')) ?: (string) ($day['title'] ?? ''), 'lat' => $lat, 'lng' => $lng]
                : null;
        }

        $places = config('places', []);
        $key = isset($places[$choice]) ? $choice : self::guess($day);

        return $key ? ['name' => $places[$key]['name'], 'lat' => (float) $places[$key]['lat'], 'lng' => (float) $places[$key]['lng']] : null;
    }

    /**
     * Place named last in the title ("Serengeti to Ngorongoro" → Ngorongoro), else in the
     * photo caption, else in the accommodation area.
     */
    public static function guess(array $day): ?string
    {
        foreach (['title', 'caption', 'stay'] as $field) {
            $text = mb_strtolower((string) ($day[$field] ?? ''));
            if ($text === '') {
                continue;
            }

            $best = null;
            foreach (config('places', []) as $key => $place) {
                foreach ($place['match'] ?? [] as $word) {
                    if (! preg_match_all('/\b'.preg_quote(mb_strtolower($word), '/').'\b/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
                        continue;
                    }
                    $end = end($matches[0])[1] + strlen($word);
                    // Later mention wins; for the same end, the longer (more specific) phrase.
                    if (! $best || $end > $best[1] || ($end === $best[1] && strlen($word) > $best[2])) {
                        $best = [$key, $end, strlen($word)];
                    }
                }
            }

            if ($best) {
                return $best[0];
            }
        }

        return null;
    }

    /**
     * Options for the admin's "Map location" select.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [self::AUTO => 'Automatic (from the day title)']
            + collect(config('places', []))->map(fn (array $place) => $place['name'])->sort()->all()
            + [self::CUSTOM => 'Other place (enter coordinates)', self::NONE => 'Don\'t show on the map'];
    }
}
