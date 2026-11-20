<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackageCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
        $this->actingAs(User::factory()->create());
    }

    public function test_completion_counts_days_written_against_the_number_of_days(): void
    {
        $this->assertSame(['planned' => 6, 'added' => 4, 'left' => 2, 'percent' => 66], Package::completionFor(6, 4));
        $this->assertSame(['planned' => 6, 'added' => 6, 'left' => 0, 'percent' => 100], Package::completionFor(6, 6));
        $this->assertSame(['planned' => 5, 'added' => 5, 'left' => 0, 'percent' => 100], Package::completionFor(3, 5)); // more days than planned
        $this->assertSame(0, Package::completionFor(null, 0)['percent']);

        // Seeded packages are complete: without a number of days, a package takes its itinerary length.
        $this->assertSame(0, Package::all()->sum(fn (Package $p) => $p->completion['left']));
    }

    public function test_admin_sees_how_much_of_the_itinerary_is_left(): void
    {
        $package = Package::where('slug', 'wildebeest-migration-ndutu-calving-6-days')->firstOrFail();
        $package->update(['itinerary' => array_slice($package->itinerary, 0, 4)]);
        $this->assertSame(6, $package->fresh()->planned_days);

        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->assertSee('4 of 6 days added')
            ->assertSee('66% complete')
            ->assertSee('2 days left');

        Livewire::test(ListPackages::class)
            ->filterTable('incomplete')
            ->assertCanSeeTableRecords([$package])
            ->assertCanNotSeeTableRecords(Package::whereKeyNot($package->id)->get())
            ->assertSee('4 / 6 days')
            ->assertSee('66% · 2 days left');

        // The website still counts the trip as 6 days for its duration filters.
        $this->assertSame(6, $package->fresh()->days);
    }

    public function test_high_end_style(): void
    {
        Package::where('slug', 'royal-wildlife-safari-8-days')->update(['style' => 'high-end']);

        $this->get(route('safaris', ['style' => 'high-end']))
            ->assertOk()
            ->assertSee('High end')
            ->assertSee('Royal Wildlife Safari in Tanzania');
    }
}
