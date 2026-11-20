<?php

namespace Tests\Feature;

use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Models\Accommodation;
use App\Models\Package;
use App\Models\User;
use App\Support\StayLinks;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccommodationPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    public function test_each_stay_has_a_page_with_description_facilities_and_gallery(): void
    {
        $stay = Accommodation::where('name', 'Central Serengeti Tented Camp')->firstOrFail();
        $stay->update(['description' => "First paragraph.\n\nSecond paragraph."]);

        $this->assertSame('central-serengeti-tented-camp', $stay->slug);
        $this->assertCount(4, $stay->photos); // main photo + 3 gallery photos

        $this->get(route('accommodations.show', $stay->slug))
            ->assertOk()
            ->assertSee('About Central Serengeti Tented Camp')
            ->assertSee('<p>First paragraph.</p>', false)
            ->assertSee('<p>Second paragraph.</p>', false)
            ->assertSee('Facilities')->assertSee('Campfire')
            ->assertSee('Photo Gallery')
            ->assertSee('data-gallery-item', false)
            ->assertSee('data-lightbox', false)
            ->assertSee('Trips That Include Central Serengeti Tented Camp')
            ->assertSee(e(route('accommodations', ['location' => 'serengeti'])), false);

        // The listing cards open the stay's page.
        $this->get(route('accommodations'))->assertSee(route('accommodations.show', $stay->slug), false);

        // Hidden or unknown stays are not found.
        $stay->update(['is_published' => false]);
        $this->get(route('accommodations.show', $stay->slug))->assertNotFound();
        $this->get(route('accommodations.show', 'no-such-stay'))->assertNotFound();
    }

    public function test_new_stays_get_a_unique_web_address(): void
    {
        $a = Accommodation::create(['name' => 'Kilima Hillside Lodgé', 'location' => 'Arusha', 'description' => 'Hotel.']);
        $b = Accommodation::create(['name' => 'Kilima Hillside Lodge', 'location' => 'Arusha', 'description' => 'Copy.']);

        $this->assertSame('kilima-hillside-lodge', $a->slug);
        $this->assertSame('kilima-hillside-lodge-2', $b->slug);

        // Renaming keeps the address, so old links keep working.
        $a->update(['name' => 'Kilima Hillside Hotel']);
        $this->assertSame('kilima-hillside-lodge', $a->fresh()->slug);
    }

    public function test_itinerary_accommodation_links_to_the_stay_page(): void
    {
        // Created automatically from the itinerary ("Gran Meliá Arusha or similar").
        $stay = Accommodation::where('name', 'Gran Meliá Arusha')->firstOrFail();
        $this->assertSame('Arusha', $stay->location);
        $this->assertSame('Luxury', $stay->level);
        $package = Package::where('slug', 'royal-wildlife-safari-8-days')->firstOrFail();

        // "Gran Meliá Arusha or similar" in the itinerary matches the stay by name.
        $links = new StayLinks;
        $this->assertTrue($stay->is($links->forOption(['name' => 'Gran Meliá Arusha or similar'])));
        $this->assertTrue($stay->is($links->forDay($package->itinerary[0])));
        $this->assertNull($links->forOption(['name' => 'Some other lodge']));

        $html = $this->get(route('safaris.show', $package->slug))->assertOk()->getContent();
        $this->assertStringContainsString('href="'.$stay->url.'" class="tm-stay-link"', $html);
        $this->assertStringContainsString('View stay &amp; photos', $html);

        // A stay picked in the admin wins over name matching.
        $other = Accommodation::where('name', 'Serengeti Luxury Lodge')->firstOrFail();
        $this->assertTrue($other->is($links->forOption(['name' => 'Gran Meliá Arusha', 'accommodation_id' => $other->id])));

        // The stay's page lists the trip.
        $this->get($stay->url)->assertSee('Trips That Include Gran Meliá Arusha')->assertSee('Royal Wildlife Safari in Tanzania');
    }

    public function test_every_itinerary_accommodation_links_to_a_page(): void
    {
        $links = new StayLinks;

        foreach (Package::published()->get() as $package) {
            foreach ($package->itinerary ?? [] as $i => $day) {
                foreach ($day['options'] ?? [] as $option) {
                    $this->assertNotNull($links->forOption($option), "{$package->slug} day ".($i + 1).": {$option['name']}");
                }
                // Days that show an accommodation (mountain huts on climbs have none).
                if (filled($day['stay'] ?? null) && (! empty($day['options']) || filled($day['stay_image'] ?? null))) {
                    $this->assertTrue($links->forDay($day) || $links->forArea($day), "{$package->slug} day ".($i + 1).' has no link');
                }
            }
        }

        // Saving a package with a new stay creates its page; running again adds nothing.
        $package = Package::where('slug', 'machame-route')->firstOrFail();
        $itinerary = $package->itinerary;
        $itinerary[0]['stay'] = 'Moshi';
        $itinerary[0]['options'] = [['level' => 'Comfort', 'name' => 'Kibo Palace Hotel or similar']];
        $package->update(['itinerary' => $itinerary]);

        $stay = Accommodation::where('name', 'Kibo Palace Hotel')->firstOrFail();
        $this->assertSame('Kilimanjaro', $stay->location);
        $this->assertSame('Mid-range', $stay->level);
        $this->get(route('safaris.show', 'machame-route'))->assertSee('href="'.$stay->url.'" class="tm-stay-link"', false);
        $this->assertSame(0, \App\Support\ItineraryStays::sync());

        // A stay the admin hid is not recreated, and its name is no longer a link.
        $stay->update(['is_published' => false]);
        $this->artisan('accommodations:from-itineraries')->expectsOutputToContain('already has a page')->assertSuccessful();
        $this->assertSame(1, Accommodation::where('name', 'Kibo Palace Hotel')->count());
        $this->get(route('safaris.show', 'machame-route'))->assertDontSee($stay->url, false);
    }

    public function test_enquire_button_opens_the_stay_website_when_set(): void
    {
        $stay = Accommodation::where('name', 'Serengeti Luxury Lodge')->firstOrFail();

        // No website: the button goes to our enquiry form.
        $this->get($stay->url)
            ->assertSee(e(route('contact', ['trip' => 'Stay: Serengeti Luxury Lodge'])).'#request', false)
            ->assertDontSee('target="_blank" rel="noopener noreferrer nofollow"', false);

        // With a website: the button opens it in a new tab.
        $stay->update(['website' => 'https://www.serengeti-lodge.example/rooms']);
        $this->get($stay->url)
            ->assertSee('href="https://www.serengeti-lodge.example/rooms" class="tm-booking-primary" target="_blank" rel="noopener noreferrer nofollow"', false)
            ->assertSee('Enquire About This Stay');

        // Anything that isn't an http(s) address is never used as a link.
        $stay->update(['website' => 'javascript:alert(1)']);
        $this->assertNull($stay->fresh()->website_url);
        $this->get($stay->url)->assertDontSee('javascript:alert(1)', false);

        // The admin form only accepts http(s) addresses.
        $this->actingAs(User::factory()->create());
        Livewire::test(ManageAccommodations::class)
            ->callTableAction('edit', $stay, ['website' => 'ftp://files.example.com'])
            ->assertHasTableActionErrors(['website']);
        Livewire::test(ManageAccommodations::class)
            ->callTableAction('edit', $stay, ['website' => 'https://www.serengeti-lodge.example'])
            ->assertHasNoTableActionErrors();
        $this->assertSame('https://www.serengeti-lodge.example', $stay->fresh()->website_url);
    }

    public function test_admin_manages_facilities_and_address(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageAccommodations::class)
            ->assertSuccessful()
            ->assertTableActionExists('view')
            ->callAction('create', [
                'name' => 'Kendwa Sunset Hotel',
                'location' => 'Zanzibar',
                'description' => 'Beach hotel near Nungwi.',
                'facilities' => ['Swimming pool', 'Beachfront'],
                'is_published' => true,
            ])
            ->assertHasNoActionErrors();

        $stay = Accommodation::where('name', 'Kendwa Sunset Hotel')->firstOrFail();
        $this->assertSame('kendwa-sunset-hotel', $stay->slug);
        $this->assertSame(['Swimming pool', 'Beachfront'], $stay->facilities);
    }
}
