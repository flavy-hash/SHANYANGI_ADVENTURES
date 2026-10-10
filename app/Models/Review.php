<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'package_id', 'name', 'country', 'travelled_on', 'rating', 'title', 'body',
        'photos', 'source_url', 'is_published', 'is_sample',
    ];

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

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Published reviews that may appear on the website. Sample (placeholder)
     * reviews are excluded in production.
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_published', true)
            ->when(app()->isProduction(), fn (Builder $q) => $q->where('is_sample', false));
    }
}
