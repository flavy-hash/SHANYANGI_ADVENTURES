<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Activity;
use App\Models\Package;
use App\Models\TripRequest;
use App\Support\AboutPage;
use App\Support\Reviews;
use App\Support\StayLinks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Website content pages. Packages, activities, accommodation, reviews and the
 * About page are managed in the admin panel (/admin). Filters come from the
 * query string, matching the links in the main navigation (e.g. /safaris?type=honeymoon).
 */
class PageController extends Controller
{
    /** Trip-length groups on the tour finder: key => [label, min days, max days]. */
    public const TOUR_LENGTHS = [
        '1-5' => ['1 to 5 days', 1, 5],
        '6-11' => ['6 to 11 days', 6, 11],
        '12-plus' => ['12+ days', 12, PHP_INT_MAX],
    ];

    public const TOUR_SORTS = [
        'recommended' => 'Recommended',
        'price-asc' => 'Price: low to high',
        'price-desc' => 'Price: high to low',
        'days-asc' => 'Shortest first',
        'days-desc' => 'Longest first',
    ];

    /** Price slider on /safaris moves in steps of this many dollars. */
    public const PRICE_STEP = 10;

    /**
     * Tour finder (/tours): every package with type and trip-length filters (several can be
     * ticked; a trip matches any ticked type and any ticked length) and sorting. Works as a
     * plain GET form, so filtered results can be shared or bookmarked.
     */
    public function tours(Request $request): View
    {
        $types = config('packages.types', []);
        $selectedTypes = array_values(array_intersect(array_map('strval', (array) $request->query('type', [])), array_keys($types)));
        $selectedLengths = array_values(array_intersect(array_map('strval', (array) $request->query('length', [])), array_keys(self::TOUR_LENGTHS)));
        $sort = array_key_exists((string) $request->query('sort'), self::TOUR_SORTS) ? (string) $request->query('sort') : 'recommended';

        $all = Package::query()->published()->ordered()->get();

        $tours = $all
            ->when($selectedTypes, fn ($items) => $items->filter(fn (Package $p) => array_intersect($p->types ?? [], $selectedTypes) !== []))
            ->when($selectedLengths, fn ($items) => $items->filter(function (Package $p) use ($selectedLengths) {
                foreach ($selectedLengths as $key) {
                    [, $min, $max] = self::TOUR_LENGTHS[$key];
                    if ($p->days >= $min && $p->days <= $max) {
                        return true;
                    }
                }

                return false;
            }));

        // Trips priced "on request" always come after priced trips when sorting by price.
        $tours = match ($sort) {
            'price-asc' => $tours->sortBy(fn (Package $p) => [$p->price ? 0 : 1, $p->price ?? 0]),
            'price-desc' => $tours->sortBy(fn (Package $p) => [$p->price ? 0 : 1, -($p->price ?? 0)]),
            'days-asc' => $tours->sortBy(fn (Package $p) => [$p->days, $p->sort_order]),
            'days-desc' => $tours->sortBy(fn (Package $p) => [-$p->days, $p->sort_order]),
            default => $tours->sortBy(fn (Package $p) => [$p->is_featured ? 0 : 1, $p->sort_order, $p->id]),
        };

        // How many trips have each option (shown next to the checkboxes).
        $typeCounts = collect(array_keys($types))->mapWithKeys(fn (string $key) => [$key => $all->filter(fn (Package $p) => in_array($key, $p->types ?? [], true))->count()]);
        $lengthCounts = collect(self::TOUR_LENGTHS)->map(fn ($range) => $all->filter(fn (Package $p) => $p->days >= $range[1] && $p->days <= $range[2])->count());

        return view('pages.tours', [
            'tours' => $tours->values(),
            'total' => $all->count(),
            'types' => $types,
            'lengths' => collect(self::TOUR_LENGTHS)->map(fn ($range) => $range[0])->all(),
            'sorts' => self::TOUR_SORTS,
            'selectedTypes' => $selectedTypes,
            'selectedLengths' => $selectedLengths,
            'sort' => $sort,
            'typeCounts' => $typeCounts,
            'lengthCounts' => $lengthCounts,
        ]);
    }

    /**
     * Safari page with a filter panel: Destination, Style and Duration rows (one choice
     * per row, combined), plus the trip type from the menu links (?type=honeymoon).
     */
    public function safaris(Request $request): View
    {
        $types = config('packages.types', []);
        $destinations = collect(config('packages.destinations', []))->map(fn ($d) => $d['label'])->all();
        $styles = config('packages.styles', []);

        $all = Package::query()->published()->ordered()->get();

        // Duration options: the trip lengths actually offered, e.g. ['3' => '3 days', ...].
        $durations = $all->pluck('days')->filter()->unique()->sort()->values()
            ->mapWithKeys(fn (int $days) => [(string) $days => $days.' '.Str::plural('day', $days)])
            ->all();

        // Price slider range: from the cheapest to the dearest "from" price, in steps of 10.
        $prices = $all->pluck('price')->filter();
        $priceRange = $prices->isNotEmpty() ? [
            'min' => (int) (floor($prices->min() / self::PRICE_STEP) * self::PRICE_STEP),
            'max' => (int) (ceil($prices->max() / self::PRICE_STEP) * self::PRICE_STEP),
        ] : null;
        $priceMax = $priceRange && is_numeric($request->query('price_max'))
            ? max($priceRange['min'], min($priceRange['max'], (int) $request->query('price_max')))
            : null;

        $active = [
            'destination' => $this->activeFilter($request->query('destination'), $destinations),
            'style' => $this->activeFilter($request->query('style'), $styles),
            'days' => $this->activeFilter($request->query('days'), $durations),
            'type' => $this->activeFilter($request->query('type'), $types),
            // Only a limit below the dearest trip filters anything.
            'price_max' => $priceMax !== null && $priceMax < $priceRange['max'] ? (string) $priceMax : null,
        ];

        $packages = $all
            ->when($active['destination'], fn ($items, $key) => $items->filter(fn (Package $p) => in_array($key, $p->destinations, true)))
            ->when($active['style'], fn ($items, $key) => $items->filter(fn (Package $p) => $p->style === $key))
            ->when($active['days'], fn ($items, $days) => $items->filter(fn (Package $p) => $p->days === (int) $days))
            ->when($active['type'], fn ($items, $key) => $items->filter(fn (Package $p) => in_array($key, $p->types ?? [], true)))
            // Trips priced "on request" have no price to compare, so a price limit leaves them out.
            ->when($active['price_max'], fn ($items, $max) => $items->filter(fn (Package $p) => $p->price && $p->price <= (int) $max))
            ->values();

        $currency = config('packages.currency', '$');

        // One row of chips per filter; each chip keeps the other rows' choices.
        $row = function (string $param, array $options, string $allLabel) use ($active) {
            $base = array_filter($active);
            $chips = [['label' => $allLabel, 'url' => route('safaris', array_diff_key($base, [$param => true])), 'active' => $active[$param] === null]];
            foreach ($options as $key => $label) {
                $chips[] = ['label' => $label, 'url' => route('safaris', [...$base, $param => (string) $key]), 'active' => $active[$param] === (string) $key];
            }

            return $chips;
        };

        return view('pages.safaris', [
            'packages' => $packages,
            'total' => $all->count(),
            'filterRows' => [
                ['label' => 'Destination', 'chips' => $row('destination', $destinations, 'All')],
                ['label' => 'Style', 'chips' => $row('style', $styles, 'All')],
                ['label' => 'Duration', 'chips' => $row('days', $durations, 'Any')],
            ],
            'priceFilter' => $priceRange ? [
                ...$priceRange,
                'step' => self::PRICE_STEP,
                'value' => $active['price_max'] ? (int) $active['price_max'] : $priceRange['max'],
                'currency' => $currency,
                // The other active filters, kept as hidden fields when the slider is used.
                'keep' => array_filter(array_diff_key($active, ['price_max' => true])),
            ] : null,
            'activePrice' => $active['price_max'] ? [
                'label' => 'Up to '.$currency.number_format((int) $active['price_max']),
                'remove' => route('safaris', array_diff_key(array_filter($active), ['price_max' => true])),
            ] : null,
            'activeType' => $active['type'] ? ['label' => $types[$active['type']], 'remove' => route('safaris', array_diff_key(array_filter($active), ['type' => true]))] : null,
            'hasFilters' => array_filter($active) !== [],
            'activeLabel' => $active['type'] ? $types[$active['type']] : ($active['destination'] ? $destinations[$active['destination']] : null),
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

        // Links from itinerary days and accommodation options to the stays' own pages.
        $stayLinks = new StayLinks;

        return view('pages.package', compact('package', 'reviews', 'related', 'stayLinks'));
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
        $all = Accommodation::query()->published()->ordered()->get();

        // Filter chips for the locations that actually have stays, e.g. ['serengeti' => 'Serengeti'].
        $locations = $all->mapWithKeys(fn (Accommodation $stay) => [$stay->location_key => $stay->location])
            ->filter(fn ($label, $key) => $key !== '')
            ->all();
        $active = $this->activeFilter($request->query('location'), $locations);

        $stays = $active ? $all->filter(fn (Accommodation $stay) => $stay->location_key === $active)->values() : $all;

        return view('pages.accommodations', [
            'stays' => $stays,
            'locations' => $locations,
            'filters' => $this->filterLinks('accommodations', 'location', $locations, $active, 'All Stays'),
            'activeLabel' => $active ? $locations[$active] : null,
        ]);
    }

    /** One stay: description, facilities, photo gallery, trips that stay there and nearby stays. */
    public function accommodation(string $slug): View
    {
        $stay = Accommodation::query()->published()->where('slug', $slug)->firstOrFail();

        $links = new StayLinks;
        $packages = Package::query()->published()->ordered()->get()
            ->filter(fn (Package $package) => $links->packageUses($package, $stay))
            ->values();

        $nearby = Accommodation::query()->published()->ordered()
            ->where('location', $stay->location)
            ->whereKeyNot($stay->getKey())
            ->take(3)
            ->get();

        return view('pages.accommodation', compact('stay', 'packages', 'nearby'));
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
