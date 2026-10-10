<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Package;
use App\Models\TripRequest;
use App\Support\AboutPage;
use App\Support\Reviews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Website content pages. Packages, activities, reviews and the About page are
 * managed in the admin panel (/admin); accommodation still comes from
 * config/accommodations.php. Filters come from the query string, matching the
 * links in the main navigation (e.g. /safaris?type=honeymoon).
 */
class PageController extends Controller
{
    public function safaris(Request $request): View
    {
        $types = config('packages.types', []);
        $active = $this->activeFilter($request->query('type'), $types);

        $packages = Package::query()->published()->ordered()
            ->when($active, fn (Builder $query) => $query->whereJsonContains('types', $active))
            ->get();

        return view('pages.safaris', [
            'packages' => $packages,
            'filters' => $this->filterLinks('safaris', 'type', $types, $active, 'All Safaris'),
            'activeLabel' => $active ? $types[$active] : null,
        ]);
    }

    /**
     * Package detail page with its day-by-day itinerary.
     */
    public function package(string $slug): View
    {
        $package = Package::query()->published()->where('slug', $slug)->firstOrFail();

        // Lets the visitor activity log record which tour was viewed.
        request()->attributes->set('viewed_package_id', $package->id);

        $reviews = Reviews::forPackage($package);

        // Similar trips first (sharing a type), then the rest.
        $related = Package::query()->published()->ordered()->whereKeyNot($package->getKey())->get()
            ->sortByDesc(fn (Package $p) => count(array_intersect($p->types ?? [], $package->types ?? [])))
            ->take(3)
            ->values();

        return view('pages.package', compact('package', 'reviews', 'related'));
    }

    public function activities(Request $request): View
    {
        $locations = config('activities.locations', []);
        $active = $this->activeFilter($request->query('location'), $locations);

        $activities = Activity::query()->published()->ordered()
            ->when($active, fn (Builder $query) => $query->where('location', $active))
            ->get();

        return view('pages.activities', [
            'activities' => $activities,
            'locations' => $locations,
            'filters' => $this->filterLinks('activities', 'location', $locations, $active, 'All Activities'),
            'activeLabel' => $active ? $locations[$active] : null,
        ]);
    }

    public function accommodations(Request $request): View
    {
        $locations = config('accommodations.locations', []);
        $active = $this->activeFilter($request->query('location'), $locations);

        $stays = collect(config('accommodations.items', []))
            ->when($active, fn (Collection $items) => $items->where('location', $active))
            ->values();

        return view('pages.accommodations', [
            'stays' => $stays,
            'locations' => $locations,
            'filters' => $this->filterLinks('accommodations', 'location', $locations, $active, 'All Stays'),
            'activeLabel' => $active ? $locations[$active] : null,
        ]);
    }

    /**
     * All guest reviews, filterable by trip type (?type=safari, kilimanjaro, ...),
     * with an overall rating summary.
     */
    public function reviews(Request $request): View
    {
        $all = Reviews::all();

        // Filter chips only for trip types that actually have reviews.
        $types = $all->pluck('category')->filter()->unique()
            ->mapWithKeys(fn (string $label) => [Str::slug($label) => $label])
            ->sort()
            ->all();
        $active = $this->activeFilter($request->query('type'), $types);

        $reviews = $active
            ? $all->filter(fn ($review) => $review['category'] === $types[$active])->values()
            : $all;

        return view('pages.reviews', [
            'reviews' => $reviews,
            'summary' => Reviews::summary($all),
            'filters' => count($types) > 1 ? $this->filterLinks('reviews', 'type', $types, $active, 'All Reviews') : [],
            'activeLabel' => $active ? $types[$active] : null,
        ]);
    }

    public function about(): View
    {
        return view('pages.about', ['about' => AboutPage::content()]);
    }

    public function contact(Request $request): View
    {
        return view('pages.contact', [
            // Pre-fill "Trip of interest" when arriving from a package/activity/stay card.
            'trip' => old('trip', mb_substr((string) $request->query('trip'), 0, 150)),
            'interests' => TripRequest::INTERESTS,
            'budgets' => TripRequest::BUDGETS,
        ]);
    }

    /**
     * Only accept known filter values; anything else shows everything.
     */
    protected function activeFilter(mixed $value, array $options): ?string
    {
        return is_string($value) && array_key_exists($value, $options) ? $value : null;
    }

    /**
     * @return array<int, array{label: string, url: string, active: bool}>
     */
    protected function filterLinks(string $route, string $param, array $options, ?string $active, string $allLabel): array
    {
        $links = [['label' => $allLabel, 'url' => route($route), 'active' => $active === null]];

        foreach ($options as $key => $label) {
            $links[] = ['label' => $label, 'url' => route($route, [$param => $key]), 'active' => $active === $key];
        }

        return $links;
    }
}
