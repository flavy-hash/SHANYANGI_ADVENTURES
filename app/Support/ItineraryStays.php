<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Package;
use Illuminate\Support\Str;

/**
 * Gives every accommodation used in an itinerary its own page, so the stay's name and
 * photo on the package page always link somewhere.
 *
 * For each accommodation option without a matching stay (by the admin's pick or by
 * name, see StayLinks) a published Accommodation is created from the itinerary: the
 * name without "or similar", the day's area, the option's comfort level and the day's
 * accommodation photo. Description and gallery can then be completed in the admin.
 * Stays the admin hid are matched too, so they are never recreated.
 */
class ItineraryStays
{
    /** Areas written in itineraries mapped to the location filters on /accommodations. */
    protected const AREAS = [
        'Lake Manyara' => ['manyara'],
        'Zanzibar' => ['zanzibar', 'stone town', 'beach', 'coast', 'nungwi', 'kendwa', 'matemwe', 'paje', 'jambiani'],
        'Serengeti' => ['serengeti', 'seronera', 'kogatende'],
        'Ngorongoro' => ['ngorongoro', 'crater'],
        'Tarangire' => ['tarangire'],
        'Karatu' => ['karatu'],
        'Arusha' => ['arusha'],
        'Kilimanjaro' => ['kilimanjaro', 'moshi'],
        'Ndutu' => ['ndutu'],
        'Lake Natron' => ['natron'],
        'Lake Eyasi' => ['eyasi'],
        'Mikumi' => ['mikumi'],
        'Nyerere' => ['nyerere', 'selous'],
        'Ruaha' => ['ruaha'],
    ];

    /** Comfort levels used in itinerary options mapped to the accommodation levels. */
    protected const LEVELS = ['premium' => 'Luxury', 'luxury' => 'Luxury', 'high end' => 'Luxury', 'comfort' => 'Mid-range', 'mid-range' => 'Mid-range', 'classic' => 'Mid-range', 'camping' => 'Budget', 'budget' => 'Budget'];

    /**
     * Create the missing stays for one package, or for all packages.
     *
     * @return int number of stays created
     */
    public static function sync(?Package $package = null): int
    {
        $packages = $package ? [$package] : Package::query()->orderBy('id')->get();
        $known = new StayLinks(Accommodation::query()->get()); // hidden stays count as existing
        $created = [];
        $order = (int) Accommodation::query()->max('sort_order');

        foreach ($packages as $p) {
            foreach ($p->itinerary ?? [] as $day) {
                foreach ($day['options'] ?? [] as $option) {
                    $name = trim(preg_replace('/\s+or similar\s*$/i', '', (string) ($option['name'] ?? '')));
                    $key = StayLinks::key($name);
                    if ($key === '' || isset($created[$key]) || $known->forOption($option)) {
                        continue;
                    }

                    $area = static::area((string) ($day['stay'] ?? ''), $name);
                    $level = static::LEVELS[Str::lower(trim((string) ($option['level'] ?? '')))] ?? null;
                    $type = static::type($name);

                    $created[$key] = Accommodation::query()->create([
                        'name' => Str::limit($name, 255, ''),
                        'location' => $area,
                        'type' => $type,
                        'level' => $level,
                        'description' => trim($name.' is one of the places we book in '.$area
                            .($level ? ', in our '.Str::lower($level).' range' : '')
                            .'. Ask us about room types, photos and availability for your dates.'),
                        'image' => filled($day['stay_image'] ?? null) && is_string($day['stay_image']) ? $day['stay_image'] : null,
                        'is_published' => true,
                        'sort_order' => ++$order,
                    ]);
                }
            }
        }

        return count($created);
    }

    /** Location filter for an itinerary area such as "Central Serengeti" or "North coast beach". */
    public static function area(string $area, string $name = ''): string
    {
        foreach ([$area, $name] as $text) {
            $text = Str::lower($text);
            foreach (static::AREAS as $label => $words) {
                foreach ($words as $word) {
                    if (str_contains($text, $word)) {
                        return $label;
                    }
                }
            }
        }

        return Str::limit($area !== '' ? $area : 'Tanzania', 80, '');
    }

    /** Type guessed from the stay's name, e.g. "Riverside tented camp, Nyerere" → "Tented camp". */
    public static function type(string $name): ?string
    {
        $name = Str::lower($name);

        return match (true) {
            str_contains($name, 'campsite') => 'Campsite',
            str_contains($name, 'tented') || str_contains($name, 'camp') => 'Tented camp',
            str_contains($name, 'villa') => 'Villa',
            str_contains($name, 'resort') || str_contains($name, 'beach') => 'Beach hotel',
            str_contains($name, 'lodge') => 'Lodge',
            str_contains($name, 'guesthouse') || str_contains($name, 'hotel') => 'Hotel',
            default => null,
        };
    }
}
