<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\TripRequest;
use Filament\Widgets\ChartWidget;

/**
 * Budget per person chosen on trip requests.
 */
class InquiryBudgetChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 8;

    protected ?string $heading = 'Inquiry budgets';

    protected ?string $description = 'Budget per person chosen on trip requests.';

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $labels = TripRequest::BUDGETS + ['none' => 'Not given'];
        $counts = TripRequest::query()->pluck('budget')->countBy(fn (?string $budget) => $budget ?: 'none');

        return [
            'datasets' => [[
                'data' => collect($labels)->keys()->map(fn (string $key) => $counts[$key] ?? 0)->all(),
                'backgroundColor' => self::palette(count($labels)),
                'borderWidth' => 0,
            ]],
            'labels' => array_values($labels),
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
