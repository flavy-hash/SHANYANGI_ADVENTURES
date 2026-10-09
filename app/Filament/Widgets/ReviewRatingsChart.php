<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\Review;
use Filament\Widgets\ChartWidget;

/**
 * Star ratings of published, real (non-sample) reviews.
 */
class ReviewRatingsChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 10;

    protected ?string $heading = 'Reviews by rating';

    protected ?string $maxHeight = '260px';

    public function getDescription(): ?string
    {
        $real = Review::query()->where('is_published', true)->where('is_sample', false);
        $count = (clone $real)->count();

        return $count
            ? 'Average '.number_format((clone $real)->avg('rating'), 1).' ★ from '.$count.' published '.str('review')->plural($count).'.'
            : 'No real reviews published yet (samples are not counted).';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $counts = Review::query()->where('is_published', true)->where('is_sample', false)->pluck('rating')->countBy();

        return [
            'datasets' => [[
                'label' => 'Reviews',
                'data' => collect([5, 4, 3, 2, 1])->map(fn (int $stars) => $counts[$stars] ?? 0)->all(),
                'backgroundColor' => self::OCHRE,
                'borderRadius' => 6,
            ]],
            'labels' => ['5 ★', '4 ★', '3 ★', '2 ★', '1 ★'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
