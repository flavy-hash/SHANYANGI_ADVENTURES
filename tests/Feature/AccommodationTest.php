<?php

namespace Tests\Feature;

use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Models\Accommodation;
use App\Models\Package;
use Filament\Forms\Components\Repeater;
use App\Models\User;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccommodationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    public function test_starter_stays_are_added_once(): void
    {
        // The 7 starter stays, plus a page for each other stay named in the itineraries.
        $this->assertSame(7, Accommodation::whereNotNull('gallery')->where('gallery', '!=', '[]')->count());
        $total = Accommodation::count();

        Accommodation::first()->update(['name' => 'Edited in the admin']);
        $this->seed(AccommodationSeeder::class);

        $this->assertSame($total, Accommodation::count());
        $this->assertTrue(Accommodation::where('name', 'Edited in the admin')->exists());
    }

    public function test_page_lists_published_stays_with_location_filters(): void
    {
        Accommodation::create(['name' => 'Tarangire Treetop Camp', 'location' => 'Tarangire', 'type' => 'Tented camp', 'level' => 'Luxury', 'description' => 'Among the baobabs.']);
        Accommodation::where('name', 'Serengeti Luxury Lodge')->update(['is_published' => false]);

        $html = $this->get(route('accommodations'))
            ->assertOk()
            ->assertSee('Tarangire Treetop Camp')
            ->assertSee(route('accommodations', ['location' => 'tarangire']), false) // new area gets a filter
            ->assertDontSee('Serengeti Luxury Lodge')
            ->getContent();
        $this->assertSame(Accommodation::published()->count(), substr_count($html, 'class="tm-info-card"'));

        // Existing menu links (?location=serengeti) keep working.
        $this->get(route('accommodations', ['location' => 'serengeti']))
            ->assertOk()
            ->assertSee('Central Serengeti Tented Camp')
            ->assertDontSee('Tarangire Treetop Camp');

        // Unknown filter shows everything.
        $this->get(route('accommodations', ['location' => 'nowhere']))->assertOk()->assertSee('Tarangire Treetop Camp');
    }

    public function test_admin_adds_and_edits_accommodation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(ManageAccommodations::getUrl())->assertOk()->assertSee('Central Serengeti Tented Camp');

        Livewire::test(ManageAccommodations::class)
            ->callAction('create', [
                'name' => 'Lake Manyara Escarpment Lodge',
                'location' => 'Lake Manyara',
                'type' => 'Lodge',
                'level' => 'Mid-range',
                'description' => 'Views over the lake from the Rift Valley wall.',
                'is_published' => true,
            ])
            ->assertHasNoActionErrors();

        $stay = Accommodation::where('name', 'Lake Manyara Escarpment Lodge')->sole();
        $this->assertSame('lake-manyara', $stay->location_key);
        $this->get(route('accommodations', ['location' => 'lake-manyara']))->assertSee('Lake Manyara Escarpment Lodge');

        // Editing a stay with an online photo keeps that photo when no new one is uploaded.
        $online = Accommodation::where('image', 'like', 'http%')->first()
            ?? tap(Accommodation::first())->update(['image' => 'https://images.example.com/lodge.jpg']);
        $photo = $online->fresh()->image;

        Livewire::test(ManageAccommodations::class)
            ->callTableAction('edit', $online, ['name' => 'Renamed Lodge'])
            ->assertHasNoTableActionErrors();

        $this->assertSame('Renamed Lodge', $online->fresh()->name);
        $this->assertSame($photo, $online->fresh()->image);
    }

    public function test_choosing_an_accommodation_in_the_itinerary_fills_photo_and_caption(): void
    {
        $this->actingAs(User::factory()->create());
        $stay = Accommodation::create(['name' => 'Tarangire Treetop Camp', 'location' => 'Tarangire', 'description' => 'Among the baobabs.', 'image' => 'uploads/accommodations/treetop.jpg']);
        $package = Package::query()->published()->firstOrFail();
        $package->update(['itinerary' => [['title' => 'Tarangire', 'text' => 'Game drive.']]]);

        Repeater::fake();
        $component = Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->set('data.itinerary.0.accommodation_id', (string) $stay->id)
            ->assertSet('data.itinerary.0.stay_caption', 'Tarangire Treetop Camp')
            ->assertSet('data.itinerary.0.stay', 'Tarangire');

        $this->assertContains('uploads/accommodations/treetop.jpg', array_values((array) $component->get('data.itinerary.0.stay_image')));

        // An area typed by hand is not overwritten.
        $component->set('data.itinerary.0.stay', 'Tarangire riverside')
            ->set('data.itinerary.0.accommodation_id', (string) Accommodation::where('name', 'Serengeti Luxury Lodge')->value('id'))
            ->assertSet('data.itinerary.0.stay', 'Tarangire riverside')
            ->assertSet('data.itinerary.0.stay_caption', 'Serengeti Luxury Lodge');
    }

    public function test_location_is_required(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageAccommodations::class)
            ->callAction('create', ['name' => 'No Place Camp', 'location' => '', 'description' => 'Somewhere.'])
            ->assertHasActionErrors(['location' => 'required']);
    }
}
