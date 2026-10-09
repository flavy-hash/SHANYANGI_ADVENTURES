<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\PageView;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Page views and (daily-unique) visitors over time.
 */
class VisitorTrendChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Website traffic';

    protected ?string $description = 'Page views and visitors. Visitors are counted once per day.';

    protected ?string $maxHeight = '300px';

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            '30d' => 'Last 30 days',
            '6m' => 'Last 6 months',
            '12m' => 'Last 12 months',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        [$start, $step, $format, $labelFormat] = match ($this->filter) {
            '6m' => [now()->subMonths(5)->startOfMonth(), '1 month', 'Y-m', 'M Y'],
            '12m' => [now()->subMonths(11)->startOfMonth(), '1 month', 'Y-m', 'M Y'],
            default => [now()->subDays(29)->startOfDay(), '1 day', 'Y-m-d', 'j M'],
        };

        // One row per day: page views + unique visitors (grouped in SQL).
        $days = PageView::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $dates = collect(CarbonPeriod::create($start, $step, now()));
        $keys = $dates->map(fn (CarbonInterface $date) => $date->format($format));

        $sum = fn (string $column) => $keys->map(fn (string $key) => (int) $days
            ->filter(fn ($row) => str_starts_with((string) $row->day, $key))
            ->sum($column))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Page views',
                    'data' => $sum('views'),
                    'borderColor' => self::OCHRE,
                    'backgroundColor' => 'rgba(176, 110, 31, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Visitors',
                    'data' => $sum('visitors'),
                    'borderColor' => self::GREEN,
                    'backgroundColor' => 'rgba(57, 86, 67, 0.18)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $dates->map(fn (CarbonInterface $date) => $date->format($labelFormat))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
