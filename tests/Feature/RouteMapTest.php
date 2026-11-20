<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Models\Package;
use App\Models\User;
use App\Support\RouteMap;
use Database\Seeders\ContentSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RouteMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    protected function package(array $days): Package
    {
        $package = Package::query()->published()->firstOrFail();
        $package->update(['itinerary' => array_map(fn ($day) => $day + ['text' => 'Text'], $days)]);

        return $package->fresh();
    }

    public function test_days_are_pinned_at_the_place_named_last(): void
    {
        $this->assertSame('ngorongoro', RouteMap::guess(['title' => 'Serengeti to Ngorongoro']));
        $this->assertSame('arusha', RouteMap::guess(['title' => 'Return to Arusha']));
        $this->assertSame('northern-serengeti', RouteMap::guess(['title' => 'Fly to the Northern Serengeti']));
        $this->assertSame('shira-2', RouteMap::guess(['title' => 'Across the Shira Plateau']));
        // Falls back to the photo caption, then the accommodation.
        $this->assertSame('tarangire', RouteMap::guess(['title' => 'Elephants', 'caption' => 'Tarangire']));
        $this->assertSame('karatu', RouteMap::guess(['title' => 'Farm day', 'stay' => 'Karatu']));
        $this->assertNull(RouteMap::guess(['title' => 'Departure']));
    }

    public function test_consecutive_days_at_one_place_share_a_pin(): void
    {
        $stops = RouteMap::stops($this->package([
            ['title' => 'Arrival in Arusha'],
            ['title' => 'Tarangire National Park'],
            ['title' => 'Into the Serengeti'],
            ['title' => 'Serengeti game drives'],
            ['title' => 'Serengeti to Ngorongoro'],
            ['title' => 'Departure'], // no place: left off the map
        ]));

        $this->assertSame(['Day 1', 'Day 2', 'Day 3-4', 'Day 5'], array_column($stops, 'label'));
        $this->assertSame('Serengeti National Park', $stops[2]['name']);
        $this->assertSame([3, 4], $stops[2]['days']);
    }

    public function test_admin_choices_override_the_automatic_place(): void
    {
        $stops = RouteMap::stops($this->package([
            ['title' => 'Arrival', 'map_place' => 'kilimanjaro-airport'],
            ['title' => 'Coffee farm visit', 'map_place' => RouteMap::CUSTOM, 'map_name' => 'Coffee Farm', 'map_lat' => '-3.25', 'map_lng' => '37.30'],
            ['title' => 'Arusha city tour', 'map_place' => RouteMap::NONE],
            ['title' => 'Bad coordinates', 'map_place' => RouteMap::CUSTOM, 'map_lat' => '999', 'map_lng' => 'x'],
        ]));

        $this->assertSame(['Kilimanjaro Airport', 'Coffee Farm'], array_column($stops, 'name'));
        $this->assertSame(-3.25, $stops[1]['lat']);
    }

    public function test_package_page_shows_the_route_map(): void
    {
        $package = $this->package([
            ['title' => 'Arrival in Arusha'],
            ['title' => 'Serengeti to Ngorongoro'],
        ]);

        $this->get(route('safaris.show', $package->slug))
            ->assertOk()
            ->assertSee('Route Map')
            ->assertSee('data-route-map', false)
            ->assertSee('Ngorongoro Crater')
            ->assertSee(e(json_encode(RouteMap::stops($package))), false);
    }

    public function test_no_map_without_at_least_two_stops(): void
    {
        $package = $this->package([['title' => 'Arrival in Arusha'], ['title' => 'Departure']]);

        $this->get(route('safaris.show', $package->slug))->assertOk()->assertDontSee('data-route-map', false);
    }

    public function test_map_location_is_saved_from_the_admin(): void
    {
        $this->actingAs(User::factory()->create());
        $package = $this->package([['title' => 'Arrival in Arusha'], ['title' => 'Beach day']]);

        Repeater::fake();
        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->fillForm(['itinerary' => [
                ['title' => 'Arrival in Arusha', 'text' => 'Text', 'map_place' => RouteMap::AUTO],
                ['title' => 'Beach day', 'text' => 'Text', 'map_place' => 'zanzibar-east'],
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['Day 1', 'Day 2'], array_column(RouteMap::stops($package->fresh()), 'label'));
        $this->assertSame('Paje, Zanzibar', RouteMap::stops($package->fresh())[1]['name']);
    }

    public function test_admin_can_pin_a_day_by_clicking_the_map(): void
    {
        $this->actingAs(User::factory()->create());
        $package = $this->package([['title' => 'Arrival in Arusha'], ['title' => 'Serengeti to Ngorongoro']]);

        // Each day has a map picker, and its script is on every admin page (so "Add day" works).
        $this->get(EditPackage::getUrl(['record' => $package]))
            ->assertOk()
            ->assertSee('shanyangiMapPicker', false)
            ->assertSee('class="sa-map-picker"', false)
            ->assertSee('leaflet.js', false);

        // What a click on the map does: custom place + coordinates. The picker itself isn't stored.
        Repeater::fake();
        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->fillForm(['itinerary' => [
                ['title' => 'Arrival in Arusha', 'text' => 'Text', 'map_place' => RouteMap::CUSTOM, 'map_name' => 'Airport lodge', 'map_lat' => '-3.41', 'map_lng' => '36.98'],
                ['title' => 'Serengeti to Ngorongoro', 'text' => 'Text'],
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $day = $package->fresh()->itinerary[0];
        $this->assertArrayNotHasKey('map_picker', $day);
        $this->assertSame(['Airport lodge', -3.41, 36.98], [RouteMap::stops($package->fresh())[0]['name'], RouteMap::stops($package->fresh())[0]['lat'], RouteMap::stops($package->fresh())[0]['lng']]);
    }
}
