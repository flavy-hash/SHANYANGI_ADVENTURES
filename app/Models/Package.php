<?php

namespace App\Models;

use App\Support\ItineraryStays;
use App\Support\RouteMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A bookable trip (safari, climb, beach stay) with its day-by-day itinerary.
 *
 * Itinerary days are stored as JSON: title, image, caption, text, note,
 * activity {title, text, image}, stay, meals, options [{level, name}],
 * stay_image, stay_caption.
 *
 * Prices are per person. With group prices (price_tiers), "price" is kept at the lowest
 * of them so cards can say "From ..."; quotes use the price for the group's size.
 */
class Package extends Model
{
    protected $fillable = [
        'slug', 'title', 'category', 'style', 'planned_days', 'tagline', 'types', 'chips', 'summary', 'price', 'price_tiers', 'child_price', 'image',
        'location', 'overview', 'facts', 'itinerary', 'included', 'excluded', 'optional_activities',
        'is_featured', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'types' => 'array',
            'chips' => 'array',
            'facts' => 'array',
            'itinerary' => 'array',
            'included' => 'array',
            'excluded' => 'array',
            'optional_activities' => 'array',
            'price' => 'integer',
            'planned_days' => 'integer',
            'price_tiers' => 'array',
            'child_price' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Every accommodation named in the itinerary gets its own page to link to.
        static::saved(fn (Package $package) => ItineraryStays::sync($package));

        static::saving(function (Package $package) {
            // Without a planned length, the trip is as long as the days written so far.
            if (! $package->planned_days && ! empty($package->itinerary)) {
                $package->planned_days = count($package->itinerary);
            }

            $tiers = collect($package->price_tiers ?? [])
                ->filter(fn ($tier) => (int) ($tier['people'] ?? 0) > 0 && (int) ($tier['price'] ?? 0) > 0)
                ->map(fn ($tier) => ['people' => (int) $tier['people'], 'price' => (int) $tier['price']])
                ->sortBy('people')
                ->values();

            $package->price_tiers = $tiers->isEmpty() ? null : $tiers->all();
            if ($tiers->isNotEmpty()) {
                $package->price = $tiers->min('price');
            }
        });
    }

    /**
     * Price per person for a group of this size: the group price for the largest group size
     * not above it (smaller groups than any listed pay the first group price), or the
     * single price when there are no group prices.
     */
    public function pricePerPerson(int $people): ?int
    {
        $tiers = $this->price_tiers ?? [];
        if ($tiers === []) {
            return $this->price ?: null;
        }

        $match = $tiers[0];
        foreach ($tiers as $tier) {
            if ($tier['people'] <= $people) {
                $match = $tier;
            }
        }

        return (int) $match['price'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Overview text split into paragraphs (blank-line separated).
     */
    protected function overviewParagraphs(): Attribute
    {
        return Attribute::get(fn () => self::paragraphs($this->overview ?: $this->summary));
    }

    /**
     * Label of the first chip, e.g. "5 Days".
     */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(fn () => $this->chips[0]['label'] ?? null);
    }

    /**
     * How far the itinerary is: days written out of the planned number of days.
     *
     * @return array{planned: int, added: int, left: int, percent: int}
     */
    public static function completionFor(?int $planned, int $added): array
    {
        $planned = max((int) $planned, $added);

        return [
            'planned' => $planned,
            'added' => $added,
            'left' => $planned - $added,
            'percent' => $planned > 0 ? (int) floor($added / $planned * 100) : 0,
        ];
    }

    protected function completion(): Attribute
    {
        return Attribute::get(fn () => static::completionFor($this->planned_days, count($this->itinerary ?? [])));
    }

    /**
     * Trip length in days: the planned number of days, else one per itinerary day,
     * else the number in the duration label.
     */
    protected function days(): Attribute
    {
        return Attribute::get(function () {
            if ($this->planned_days) {
                return $this->planned_days;
            }

            $fromItinerary = count($this->itinerary ?? []);

            return $fromItinerary ?: (int) (preg_match('/(\d+)\s*day/i', (string) $this->duration_label, $m) ? $m[1] : 0);
        });
    }

    /**
     * Destination keys this package belongs to (config('packages.destinations')): by category,
     * trip type, or a place its itinerary route passes through. Value: list of keys.
     */
    protected function destinations(): Attribute
    {
        return Attribute::get(function () {
            $route = implode(' | ', array_column(RouteMap::stops($this), 'name'));
            $keys = [];

            foreach (config('packages.destinations', []) as $key => $rule) {
                $match = in_array($this->category, $rule['categories'] ?? [], true)
                    || array_intersect($this->types ?? [], $rule['types'] ?? []) !== []
                    || collect($rule['places'] ?? [])->contains(fn ($place) => str_contains($route, $place));

                if ($match) {
                    $keys[] = $key;
                }
            }

            return $keys;
        });
    }

    /**
     * Short selling points for cards: the tagline plus the chips after the duration.
     */
    protected function highlights(): Attribute
    {
        return Attribute::get(fn () => array_values(array_filter([
            $this->tagline,
            ...array_map(fn ($chip) => $chip['label'] ?? null, array_slice($this->chips ?? [], 1)),
        ])));
    }

    /**
     * The route in a line, from the itinerary's map stops: ["Arusha", "Tarangire", "2 days Serengeti", ...].
     */
    protected function routeSummary(): Attribute
    {
        return Attribute::get(fn () => array_map(function (array $stop) {
            $name = preg_replace('/ National Park$/', '', $stop['name']);
            $count = count($stop['days']);

            return $count > 1 ? "{$count} days {$name}" : $name;
        }, RouteMap::stops($this)));
    }

    public static function paragraphs(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $text))));
    }
}
