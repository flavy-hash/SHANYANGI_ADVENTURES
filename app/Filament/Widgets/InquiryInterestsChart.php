<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\TripRequest;
use Filament\Widgets\ChartWidget;

/**
 * What travellers tick under "What are you interested in?" on the inquiry form.
 */
class InquiryInterestsChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 7;

    protected ?string $heading = 'What travellers are interested in';

    protected ?string $description = 'From the interests ticked on trip requests.';

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $counts = TripRequest::query()->pluck('interests')
            ->flatten()
            ->filter()
            ->countBy();

        return [
            'datasets' => [[
                'label' => 'Inquiries',
                'data' => collect(TripRequest::INTERESTS)->keys()->map(fn (string $key) => $counts[$key] ?? 0)->all(),
                'backgroundColor' => self::GREEN,
                'borderRadius' => 6,
            ]],
            'labels' => array_values(TripRequest::INTERESTS),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
