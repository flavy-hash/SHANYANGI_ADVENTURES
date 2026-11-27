<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $fillable = [
        'package_id', 'name', 'country', 'email', 'travelled_on', 'rating', 'title', 'body',
        'photos', 'source_url', 'source', 'is_published', 'is_sample',
    ];

    protected $hidden = ['email'];

    protected function casts(): array
    {
        return [
            'travelled_on' => 'date',
            'rating' => 'integer',
            'photos' => 'array',
            'is_published' => 'boolean',
            'is_sample' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Remove uploaded photos with the review (e.g. deleting spam). Seed images are shared, so kept.
        static::deleted(function (Review $review) {
            $uploads = array_filter($review->photos ?? [], fn ($path) => is_string($path) && str_starts_with($path, 'uploads/'));
            if ($uploads) {
                Storage::disk('media')->delete($uploads);
            }
        });
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Published reviews that may appear on the website. Sample (placeholder)
     * reviews are excluded in production.
     */
    /**
     * Reviews written on the website that nobody has published yet.
     */
    public function scopeAwaitingApproval(Builder $query): void
    {
        $query->where('source', 'website')->where('is_published', false);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_published', true)
            ->when(app()->isProduction(), fn (Builder $q) => $q->where('is_sample', false));
    }
}
