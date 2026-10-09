<?php

namespace App\Filament\Widgets\Concerns;

/**
 * Website palette for dashboard charts (Chart.js needs explicit colours).
 */
trait BrandColors
{
    protected const GREEN = '#395643';

    protected const FOREST = '#23331f';

    protected const OCHRE = '#b06e1f';

    protected const SAND = '#d9b98a';

    protected const BEIGE = '#e9e1cb';

    /**
     * Distinct colours for pie / doughnut slices. No near-black shades, so
     * every slice stays visible in the admin's dark mode too.
     *
     * @return list<string>
     */
    protected static function palette(int $count): array
    {
        $colors = ['#395643', '#b06e1f', '#7fa173', '#d9b98a', '#4f7c82', '#c2703d', '#a8b89f', '#8a6d4b'];

        return array_slice(array_merge($colors, $colors), 0, max(1, $count));
    }
}
