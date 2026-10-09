<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\NewsletterSubscriber;
use App\Models\TripRequest;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

/**
 * Inquiries and newsletter sign-ups over time (daily for 30 days, monthly otherwise).
 */
class ActivityTrendChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Inquiries & newsletter sign-ups';

    protected ?string $description = 'How many trip requests and subscribers the website brings in.';

    protected ?string $maxHeight = '300px';

    public ?string $filter = '12m';

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
            '30d' => [now()->subDays(29)->startOfDay(), '1 day', 'Y-m-d', 'j M'],
            '6m' => [now()->subMonths(5)->startOfMonth(), '1 month', 'Y-m', 'M Y'],
            default => [now()->subMonths(11)->startOfMonth(), '1 month', 'Y-m', 'M Y'],
        };

        $dates = collect(CarbonPeriod::create($start, $step, now()));
        $buckets = $dates->mapWithKeys(fn (CarbonInterface $date) => [$date->format($format) => 0]);

        $count = function (string $model) use ($start, $format, $buckets): array {
            /** @var class-string<Model> $model */
            $counts = $model::query()->where('created_at', '>=', $start)->pluck('created_at')
                ->countBy(fn (CarbonInterface $date) => $date->format($format));

            return $buckets->map(fn ($zero, $key) => $counts[$key] ?? 0)->values()->all();
        };

        return [
            'datasets' => [
                [
                    'label' => 'Inquiries',
                    'data' => $count(TripRequest::class),
                    'borderColor' => self::GREEN,
                    'backgroundColor' => 'rgba(57, 86, 67, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Newsletter sign-ups',
                    'data' => $count(NewsletterSubscriber::class),
                    'borderColor' => self::OCHRE,
                    'backgroundColor' => 'rgba(176, 110, 31, 0.08)',
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
