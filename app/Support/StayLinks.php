<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Finds the accommodation page an itinerary day or accommodation option refers to.
 *
 * An explicit choice in the admin (accommodation_id on the day or option) wins.
 * Otherwise names are compared ignoring case, accents, punctuation, word order
 * and a trailing "or similar", so "Gran Meliá Arusha or similar" finds the stay
 * "Gran Melia Arusha" and "Tented camp, Central Serengeti" finds
 * "Central Serengeti Tented Camp". Only published stays are linked.
 */
class StayLinks
{
    /** @var Collection<int, Accommodation> */
    protected Collection $byId;

    /** @var array<string, Accommodation> */
    protected array $byName = [];

    public function __construct(?Collection $stays = null)
    {
        $stays ??= Accommodation::query()->published()->ordered()->get();

        $this->byId = $stays->keyBy('id');
        foreach ($stays as $stay) {
            $this->byName[static::key($stay->name)] ??= $stay;
        }
    }

    /** Comparable form of a name: lower-case ASCII words, sorted, without "or similar". */
    public static function key(?string $name): string
    {
        $text = Str::of(Str::ascii((string) $name))->lower()->replaceMatches('/\bor similar\b/', ' ')->replaceMatches('/[^a-z0-9]+/', ' ')->trim();
        $words = array_filter(explode(' ', (string) $text));
        sort($words);

        return implode(' ', $words);
    }

    public function find(mixed $id = null, ?string $name = null): ?Accommodation
    {
        if (filled($id) && ($stay = $this->byId->get((int) $id))) {
            return $stay;
        }

        $key = static::key($name);

        return $key !== '' ? ($this->byName[$key] ?? null) : null;
    }

    public function forOption(array $option): ?Accommodation
    {
        return $this->find($option['accommodation_id'] ?? null, $option['name'] ?? null);
    }

    /** The day's stay: the one picked for the day, its photo caption, or the first linked option. */
    public function forDay(array $day): ?Accommodation
    {
        if ($stay = $this->find($day['accommodation_id'] ?? null, $day['stay_caption'] ?? null)) {
            return $stay;
        }

        foreach ($day['options'] ?? [] as $option) {
            if ($stay = $this->forOption($option)) {
                return $stay;
            }
        }

        return null;
    }

    /**
     * For a day that names no particular stay: the Accommodation page filtered to the
     * day's area (e.g. /accommodations?location=zanzibar), when stays are listed there.
     *
     * @return array{url: string, label: string}|null
     */
    public function forArea(array $day): ?array
    {
        if (blank($day['stay'] ?? null)) {
            return null;
        }

        $area = ItineraryStays::area((string) $day['stay']);
        $key = Str::slug($area);

        return $this->byId->contains(fn (Accommodation $stay) => $stay->location_key === $key)
            ? ['url' => route('accommodations', ['location' => $key]), 'label' => 'Stays in '.$area]
            : null;
    }

    /** Whether any day or option of the package links to this stay. */
    public function packageUses(Package $package, Accommodation $stay): bool
    {
        foreach ($package->itinerary ?? [] as $day) {
            if ($this->find($day['accommodation_id'] ?? null, $day['stay_caption'] ?? null)?->is($stay)) {
                return true;
            }
            foreach ($day['options'] ?? [] as $option) {
                if ($this->forOption($option)?->is($stay)) {
                    return true;
                }
            }
        }

        return false;
    }
}
