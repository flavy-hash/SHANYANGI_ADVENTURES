<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Models\Package;
use App\Models\Quote;
use App\Models\User;
use App\Support\QuotePdf;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class GroupPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    protected function migrationSafari(): Package
    {
        return Package::where('slug', 'wildebeest-great-migration-luxury-safari-4-days')->firstOrFail();
    }

    public function test_group_prices_are_sorted_and_the_from_price_is_the_lowest(): void
    {
        $package = $this->migrationSafari();
        $package->update(['price' => 1, 'price_tiers' => [
            ['people' => 6, 'price' => 3146], ['people' => 2, 'price' => '3771'], ['people' => 4, 'price' => 3303], ['people' => '', 'price' => ''],
        ]]);

        $package->refresh();
        $this->assertSame([['people' => 2, 'price' => 3771], ['people' => 4, 'price' => 3303], ['people' => 6, 'price' => 3146]], $package->price_tiers);
        $this->assertSame(3146, $package->price);

        // Smaller groups than listed pay the first price; groups in between pay the smaller size's price.
        $this->assertSame(3771, $package->pricePerPerson(1));
        $this->assertSame(3771, $package->pricePerPerson(3));
        $this->assertSame(3303, $package->pricePerPerson(5));
        $this->assertSame(3146, $package->pricePerPerson(9));

        // Without group prices, the single price applies.
        $package->update(['price_tiers' => [], 'price' => 2000]);
        $this->assertNull($package->fresh()->price_tiers);
        $this->assertSame(2000, $package->fresh()->pricePerPerson(4));
    }

    public function test_quote_lines_use_the_group_size_and_the_child_price(): void
    {
        $package = $this->migrationSafari();

        $items = QuotePdf::packageItems($package, 2, 0);
        $this->assertEquals(3771, $items[0]['unit_price']);

        $items = QuotePdf::packageItems($package, 4, 2);
        $this->assertEquals(3146, $items[0]['unit_price']); // six travelling
        $this->assertEquals(3146 * config('quotes.child_rate_percent') / 100, $items[1]['unit_price']);

        $family = Package::where('slug', 'luxury-family-safari-great-migration-10-days')->firstOrFail();
        $items = QuotePdf::packageItems($family, 2, 2);
        $this->assertEquals(8193, $items[0]['unit_price']);
        $this->assertEquals(5043, $items[1]['unit_price']);
    }

    public function test_package_page_lists_group_prices_and_the_instant_quote_uses_them(): void
    {
        Mail::fake();
        $package = $this->migrationSafari();

        $this->get(route('safaris.show', $package->slug))
            ->assertOk()
            ->assertSee('$3,146')
            ->assertSeeInOrder(['2 people', '$3,771 per person', '4 people', '$3,303 per person', '6 people', '$3,146 per person'])
            ->assertSee('Priced per person for your group size');

        $this->get(route('safaris.show', 'luxury-family-safari-great-migration-10-days'))
            ->assertSee('$5,043 per child');

        $this->post(route('quotes.store', $package->slug), [
            'name' => 'Jane Traveller', 'email' => 'jane@example.com',
            'travel_date' => now()->addMonths(3)->toDateString(), 'adults' => 2, 'children' => 0,
        ]);
        $this->assertEquals(3771 * 2, Quote::sole()->total);
    }

    public function test_admin_edits_group_prices(): void
    {
        $this->actingAs(User::factory()->create());
        $package = $this->migrationSafari();

        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->assertFormFieldExists('price_tiers')
            ->assertFormFieldExists('child_price')
            ->set('data.child_price', 2500)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(2500, $package->fresh()->child_price);
        $this->assertSame(3146, $package->fresh()->price);
    }

    public function test_your_itineraries_are_seeded_with_their_prices(): void
    {
        $expected = [
            'calving-season-ndutu-safari-5-days' => [2425, 1927, 1762],
            'wildebeest-migration-ndutu-calving-6-days' => [3014, 2422, 2226],
            'zanzibar-beach-honeymoon-holiday-7-days' => [1617, 1360, 1341],
            'royal-wildlife-safari-8-days' => [6204, 5487, 5250],
            'luxury-wildlife-cultural-safari-8-days' => [4329, 3746, 3552],
            'ultimate-wildlife-luxury-experience-8-days' => [6719, 5931, 5669],
            'great-migration-luxury-safari-9-days' => [7873, 7000, 6705],
            'best-of-tanzania-and-zanzibar-11-days' => [4584, 3880, 3675],
            'premium-luxury-safari-and-beach-12-days' => [7912, 7252, 7067],
        ];

        foreach ($expected as $slug => $prices) {
            $package = Package::where('slug', $slug)->firstOrFail();
            $this->assertSame($prices, array_column($package->price_tiers, 'price'), $slug);
            $this->assertSame(min($prices), $package->price, $slug);
        }

        // No other company's name in the seeded text.
        $this->assertSame(0, Package::where('itinerary', 'like', '%Lights on Africa%')->count());
    }
}
