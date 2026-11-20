<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Lodges, camps and hotels listed on the Accommodation page (/accommodations),
 * each with its own page (/accommodations/{slug}) showing the description,
 * facilities and photo gallery. Itinerary days link to these pages.
 */
class Accommodation extends Model
{
    /** Suggestions in the admin; any other value can be typed. */
    public const LOCATIONS = ['Arusha', 'Tarangire', 'Lake Manyara', 'Karatu', 'Ngorongoro', 'Serengeti', 'Kilimanjaro', 'Zanzibar'];

    public const TYPES = ['Lodge', 'Tented camp', 'Farm lodge', 'Boutique hotel', 'Beach hotel', 'Villa', 'Mountain camp'];

    public const LEVELS = ['Budget', 'Mid-range', 'Luxury'];

    public const FACILITIES = ['Swimming pool', 'Wi-Fi', 'Restaurant', 'Bar', 'Spa', 'Laundry', 'Airport transfers', 'Family rooms', 'En-suite bathrooms', 'Views of the park', 'Beachfront', 'Campfire'];

    protected $fillable = ['name', 'slug', 'location', 'type', 'level', 'description', 'website', 'image', 'gallery', 'facilities', 'is_published', 'sort_order'];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'facilities' => 'array',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // New stays get a web address from their name; it then stays the same so links keep working.
        static::saving(function (Accommodation $stay) {
            if (blank($stay->slug)) {
                $stay->slug = static::uniqueSlug((string) $stay->name, $stay->getKey());
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'stay';
        $slug = $base;
        for ($i = 2; static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /** Filter key used in ?location= links, e.g. "Lake Manyara" → "lake-manyara". */
    protected function locationKey(): Attribute
    {
        return Attribute::get(fn () => Str::slug((string) $this->location));
    }

    /** The stay's own page. */
    protected function url(): Attribute
    {
        return Attribute::get(fn () => route('accommodations.show', $this->slug));
    }

    /** The stay's own website, only when it is a normal http(s) address. */
    protected function websiteUrl(): Attribute
    {
        return Attribute::get(fn () => preg_match('#^https?://[^\s]+$#i', trim((string) $this->website)) ? trim($this->website) : null);
    }

    /** Main photo followed by the gallery photos. */
    protected function photos(): Attribute
    {
        return Attribute::get(fn () => array_values(array_unique(array_filter([$this->image, ...($this->gallery ?? [])]))));
    }

    /** Description split into paragraphs at blank lines. */
    protected function paragraphs(): Attribute
    {
        return Attribute::get(fn () => array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $this->description)))));
    }
}
