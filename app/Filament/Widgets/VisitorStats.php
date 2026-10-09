<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PageViews\PageViewResource;
use App\Models\PageView;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * Website traffic at a glance. "Visitors" are daily-unique anonymous IDs
 * (they rotate daily, so multi-day totals add up daily visitors).
 */
class VisitorStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Website visitors';

    protected function getStats(): array
    {
        $today = PageView::query()->whereDate('created_at', today());
        $month = PageView::query()->where('created_at', '>=', now()->subDays(29)->startOfDay());

        $visitorsToday = (clone $today)->distinct()->count('visitor_hash');
        $viewsToday = (clone $today)->count();

        // Sum of daily unique visitors over 30 days.
        $visitors30 = (int) PageView::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(DISTINCT visitor_hash) as visitors')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->sum('visitors');

        $topTour = (clone $month)->whereNotNull('package_id')
            ->select('package_id', DB::raw('COUNT(*) as views'))
            ->groupBy('package_id')
            ->orderByDesc('views')
            ->with('package:id,title')
            ->first();

        return [
            Stat::make('Visitors today', $visitorsToday)
                ->description($viewsToday.' page '.str('view')->plural($viewsToday))
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->url(PageViewResource::getUrl()),
            Stat::make('Visitors (last 30 days)', number_format($visitors30))
                ->description(number_format((clone $month)->count()).' page views')
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->url(PageViewResource::getUrl()),
            Stat::make('Most viewed tour (30 days)', $topTour?->package?->title ?? '-')
                ->description($topTour ? $topTour->views.' views' : 'No tour views yet')
                ->descriptionIcon(Heroicon::OutlinedMap),
        ];
    }
}
