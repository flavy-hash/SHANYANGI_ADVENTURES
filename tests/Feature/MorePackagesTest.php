<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Support\RouteMap;
use Database\Seeders\ContentSeeder;
use Database\Seeders\MorePackagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MorePackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    /** Every package from the content/packages-more*.php files. */
    protected function contentPackages(): array
    {
        $packages = [];
        foreach (['packages-more', 'packages-more-2', 'packages-more-3', 'packages-more-4'] as $file) {
            $packages = [...$packages, ...require database_path("seeders/content/{$file}.php")];
        }

        return $packages;
    }

    public function test_additional_packages_are_added_once_and_never_overwrite_edits(): void
    {
        $slugs = array_column($this->contentPackages(), 'slug');
        $this->assertSame(count($slugs), count(array_unique($slugs)));
        $this->assertSame(count($slugs), Package::whereIn('slug', $slugs)->count());

        Package::where('slug', 'grand-tanzania-12-days')->update(['title' => 'Edited title', 'price' => 4200]);
        $total = Package::count();

        $this->seed(MorePackagesSeeder::class);

        $this->assertSame($total, Package::count());
        $this->assertSame('Edited title', Package::where('slug', 'grand-tanzania-12-days')->value('title'));
        $this->assertSame(4200, Package::where('slug', 'grand-tanzania-12-days')->value('price'));
    }

    public function test_every_new_package_page_shows_its_full_itinerary(): void
    {
        foreach ($this->contentPackages() as $p) {
            $package = Package::where('slug', $p['slug'])->firstOrFail();
            $response = $this->get(route('safaris.show', $package->slug))->assertOk()->assertSee(e($package->title), false);

            $this->assertCount(count($p['itinerary']), $package->itinerary);
            foreach ($p['itinerary'] as $i => $day) {
                $response->assertSee(e($day['title']), false);
                $this->assertNotNull(RouteMap::placeFor($package->itinerary[$i]), "{$p['slug']} day ".($i + 1).' has no map place');
            }

            // Multi-stop trips get a route map; every day of a trip lands on the map or is a single-place day trip.
            if (count(RouteMap::stops($package)) >= 2) {
                $response->assertSee('data-route-map', false);
            }
        }
    }

    public function test_marangu_huts_are_on_the_route_map(): void
    {
        $names = array_column(RouteMap::stops(Package::where('slug', 'marangu-route-6-days')->firstOrFail()), 'name');

        $this->assertSame(['Mandara Hut', 'Horombo Hut', 'Kibo Hut', 'Uhuru Peak', 'Marangu Gate'], $names);
    }

    public function test_new_kilimanjaro_routes_and_zanzibar_holidays_are_filed_correctly(): void
    {
        $names = array_column(RouteMap::stops(Package::where('slug', 'rongai-route-7-days')->firstOrFail()), 'name');
        $this->assertSame(['Simba Camp', 'Kikelewa Caves', 'Mawenzi Tarn', 'School Hut', 'Uhuru Peak', 'Marangu Gate'], $names);

        $meru = Package::where('slug', 'mount-meru-climb-4-days')->firstOrFail();
        $this->assertContains('kilimanjaro', $meru->destinations);

        foreach (['zanzibar-honeymoon-6-days', 'zanzibar-family-beach-7-days', 'stone-town-short-break-3-days'] as $slug) {
            $this->assertSame(['zanzibar'], Package::where('slug', $slug)->firstOrFail()->destinations, $slug);
        }

        $this->assertContains('safaris', Package::where('slug', 'southern-tanzania-nyerere-ruaha-7-days')->firstOrFail()->destinations);
    }
}
