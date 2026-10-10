<?php

namespace App\Models;

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
 */
class Package extends Model
{
    protected $fillable = [
        'slug', 'title', 'category', 'tagline', 'types', 'chips', 'summary', 'price', 'image',
        'location', 'overview', 'facts', 'itinerary', 'included', 'excluded',
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
            'price' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
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

    public static function paragraphs(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $text))));
    }
}
