<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\Package;
use Filament\Widgets\ChartWidget;

/**
 * Published packages per category.
 */
class PackagesByCategoryChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 9;

    protected ?string $heading = 'Packages by category';

    protected ?string $description = 'Published packages on the website.';

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $counts = Package::query()->published()->pluck('category')->countBy()->sortDesc();

        return [
            'datasets' => [[
                'data' => $counts->values()->all(),
                'backgroundColor' => self::palette($counts->count()),
                'borderWidth' => 0,
            ]],
            'labels' => $counts->keys()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'right']],
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }
}
