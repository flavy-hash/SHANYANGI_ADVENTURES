<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BrandColors;
use App\Models\Package;
use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Tour (package) pages ranked by views.
 */
class TopToursChart extends ChartWidget
{
    use BrandColors;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Most viewed tours';

    protected ?string $maxHeight = '280px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return ['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days'];
    }

    public function getDescription(): ?string
    {
        return 'Views of each tour page.';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $rows = PageView::query()
            ->whereNotNull('package_id')
            ->where('created_at', '>=', now()->subDays((int) $this->filter - 1)->startOfDay())
            ->select('package_id', DB::raw('COUNT(*) as views'))
            ->groupBy('package_id')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        $titles = Package::query()->whereKey($rows->pluck('package_id'))->pluck('title', 'id');

        return [
            'datasets' => [[
                'label' => 'Views',
                'data' => $rows->pluck('views')->map(fn ($v) => (int) $v)->all(),
                'backgroundColor' => self::GREEN,
                'borderRadius' => 6,
            ]],
            'labels' => $rows->map(fn ($row) => $titles[$row->package_id] ?? 'Deleted tour')->all(),
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
