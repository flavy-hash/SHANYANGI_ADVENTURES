<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One anonymous page view (see App\Http\Middleware\LogPageView).
 * Deleted automatically after config('analytics.retention_days') by `model:prune`.
 */
class PageView extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    public const PAGE_TYPES = [
        'home' => 'Home',
        'safaris' => 'Safaris list',
        'package' => 'Tour / package',
        'activities' => 'Activities',
        'accommodations' => 'Accommodation',
        'blog' => 'Blog list',
        'blog-post' => 'Blog article',
        'about' => 'About us',
        'reviews' => 'Reviews',
        'contact' => 'Contact',
        'other' => 'Other',
    ];

    protected $fillable = ['visitor_hash', 'path', 'page_type', 'package_id', 'device', 'referrer_host', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(config('analytics.retention_days')));
    }
}
