<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Filament\Resources\TripRequests\TripRequestResource;
use App\Models\NewsletterSubscriber;
use App\Models\Package;
use App\Models\Review;
use App\Models\TripRequest;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $newInquiries = TripRequest::query()->whereNull('handled_at')->count();

        return [
            Stat::make('New inquiries', $newInquiries)
                ->description(TripRequest::query()->where('created_at', '>=', now()->subDays(30))->count().' in the last 30 days')
                ->descriptionIcon(Heroicon::OutlinedInboxArrowDown)
                ->color($newInquiries ? 'warning' : 'success')
                ->url(TripRequestResource::getUrl()),
            Stat::make('Newsletter subscribers', NewsletterSubscriber::query()->whereNull('unsubscribed_at')->count())
                ->description(NewsletterSubscriber::query()->where('created_at', '>=', now()->subDays(30))->count().' joined in the last 30 days')
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->url(NewsletterSubscriberResource::getUrl()),
            Stat::make('Published packages', Package::query()->published()->count())
                ->description(Package::query()->where('is_featured', true)->count().' featured on the home page')
                ->descriptionIcon(Heroicon::OutlinedMap)
                ->url(PackageResource::getUrl()),
            Stat::make('Published reviews', Review::query()->where('is_published', true)->where('is_sample', false)->count())
                ->description(Review::query()->where('is_sample', true)->count().' sample placeholders')
                ->descriptionIcon(Heroicon::OutlinedStar)
                ->url(ReviewResource::getUrl()),
        ];
    }
}
