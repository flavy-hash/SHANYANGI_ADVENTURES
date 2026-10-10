<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value page content edited in the admin (e.g. the About Us page).
 */
class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (SiteSetting $setting) => Cache::forget('site_setting.'.$setting->key));
        static::deleted(fn (SiteSetting $setting) => Cache::forget('site_setting.'.$setting->key));
    }

    /**
     * Stored value merged over $defaults, so new fields always have a fallback.
     */
    public static function get(string $key, array $defaults = []): array
    {
        return array_replace($defaults, array_filter((array) static::raw($key), fn ($v) => $v !== null && $v !== ''));
    }

    /**
     * The stored value exactly as saved, or null if it was never saved.
     */
    public static function raw(string $key): ?array
    {
        return Cache::rememberForever('site_setting.'.$key, fn () => static::query()->where('key', $key)->value('value'));
    }

    public static function put(string $key, array $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
