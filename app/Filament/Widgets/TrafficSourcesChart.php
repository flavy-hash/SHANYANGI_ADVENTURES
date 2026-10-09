<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\PageView;
use Filament\Widgets\ChartWidget;

/**
 * Where visitors arrive from (last 30 days), grouped into readable sources.
 */
class TrafficSourcesChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 5;

    protected ?string $heading = 'Where visitors come from';

    protected ?string $description = 'Last 30 days, based on the referring website.';

    protected ?string $maxHeight = '280px';

    protected const GROUPS = [
        'Search engines' => '/(^|\.)(google|bing|yahoo|duckduckgo|ecosia|yandex|baidu)\./',
        'Social media' => '/(^|\.)(facebook|fb|instagram|linkedin|lnkd|t|twitter|x|tiktok|youtube|pinterest|whatsapp|wa)\.(com|me|co|in|net)$/',
        'Review sites' => '/(^|\.)(tripadvisor|safaribookings|trustpilot)\./',
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function group(?string $host): string
    {
        if (! $host) {
            return 'Direct';
        }

        foreach (self::GROUPS as $label => $pattern) {
            if (preg_match($pattern, $host)) {
                return $label;
            }
        }

        return 'Other websites';
    }

    protected function getData(): array
    {
        $counts = PageView::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('referrer_host, COUNT(*) as views')
            ->groupBy('referrer_host')
            ->get()
            ->groupBy(fn ($row) => self::group($row->referrer_host))
            ->map(fn ($rows) => (int) $rows->sum('views'));

        $labels = ['Direct', 'Search engines', 'Social media', 'Review sites', 'Other websites'];

        return [
            'datasets' => [[
                'data' => array_map(fn (string $label) => $counts[$label] ?? 0, $labels),
                'backgroundColor' => self::palette(count($labels)),
                'borderWidth' => 0,
            ]],
            'labels' => $labels,
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
