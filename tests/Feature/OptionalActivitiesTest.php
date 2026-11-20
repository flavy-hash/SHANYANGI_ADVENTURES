<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OptionalActivitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    public function test_optional_activities_have_their_own_section_below_the_itinerary(): void
    {
        $package = Package::where('slug', 'royal-wildlife-safari-8-days')->firstOrFail();

        $this->assertCount(6, $package->optional_activities);
        $this->assertSame([], array_values(array_filter($package->excluded, fn ($item) => str_starts_with($item, 'Optional'))));

        $this->get(route('safaris.show', $package->slug))
            ->assertOk()
            ->assertSeeInOrder(['Day-by-Day Itinerary', 'Optional Activities', 'Materuni Waterfalls', 'USD 150 per person sharing', 'What\'s Included'])
            ->assertSee('Hot air balloon (Serengeti)');

        // Packages without extras don't show the section.
        $this->get(route('safaris.show', 'machame-route'))->assertOk()->assertDontSee('Optional Activities');

        // No seeded package keeps "Optional: ..." lines under "Not included".
        foreach (Package::all() as $p) {
            $this->assertSame([], array_values(array_filter($p->excluded ?? [], fn ($item) => str_starts_with($item, 'Optional:'))), $p->slug);
        }
    }

    public function test_admin_edits_optional_activities(): void
    {
        $this->actingAs(User::factory()->create());
        $package = Package::where('slug', 'machame-route')->firstOrFail();

        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->assertFormFieldExists('optional_activities')
            ->set('data.optional_activities', [['name' => 'Materuni Waterfalls', 'price' => 'USD 150 per person']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([['name' => 'Materuni Waterfalls', 'price' => 'USD 150 per person']], $package->fresh()->optional_activities);
        $this->get(route('safaris.show', 'machame-route'))->assertSee('Optional Activities')->assertSee('Materuni Waterfalls');
    }
}
