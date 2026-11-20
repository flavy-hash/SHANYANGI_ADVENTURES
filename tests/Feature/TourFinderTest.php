<?php

namespace Tests\Feature;

use App\Models\Package;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    /** Titles of the trip cards on a /tours response, in order. */
    protected function titles(string $url): array
    {
        preg_match_all('#<h3 class="tm-tour-title"><a [^>]+>(.*?)</a></h3>#', $this->get($url)->assertOk()->getContent(), $m);

        return array_map(fn ($t) => html_entity_decode($t), $m[1]);
    }

    public function test_lists_every_published_trip_with_price_route_and_link(): void
    {
        $published = Package::query()->published()->count();
        $package = Package::query()->where('slug', 'serengeti-ngorongoro-safari')->firstOrFail();

        $this->assertCount($published, $this->titles(route('tours')));

        $this->get(route('tours'))
            ->assertSee('Tanzania Tours')
            ->assertSee('$1,650')
            ->assertSee('On request')
            ->assertSee('2 days Serengeti') // route line from the itinerary
            ->assertSee(route('safaris.show', $package->slug), false)
            ->assertSee('Popular');

        // Hidden packages are not listed.
        $package->update(['is_published' => false]);
        $this->assertNotContains($package->title, $this->titles(route('tours')));
    }

    public function test_filters_by_type_and_length(): void
    {
        $ofType = fn (array $types) => Package::published()->get()->filter(fn ($p) => array_intersect($p->types ?? [], $types))->pluck('title')->all();

        $kili = $this->titles(route('tours', ['type' => ['kilimanjaro']]));
        $this->assertEqualsCanonicalizing($ofType(['kilimanjaro']), $kili);
        $this->assertContains('Machame Route', $kili);

        // Several types: a trip matching any of them is shown.
        $this->assertEqualsCanonicalizing($ofType(['kilimanjaro', 'migration']), $this->titles(route('tours', ['type' => ['kilimanjaro', 'migration']])));

        // Length: 6 to 11 days.
        foreach ($this->titles(route('tours', ['length' => ['6-11']])) as $title) {
            $days = Package::where('title', $title)->first()->days;
            $this->assertTrue($days >= 6 && $days <= 11, "$title has $days days");
        }

        // Type AND length together (longest first).
        $expected = Package::published()->get()
            ->filter(fn ($p) => in_array('kilimanjaro', $p->types ?? [], true) && $p->days >= 6 && $p->days <= 11)
            ->sortByDesc('days')->pluck('title')->values()->all();
        $this->assertSame($expected, $this->titles(route('tours', ['type' => ['kilimanjaro'], 'length' => ['6-11'], 'sort' => 'days-desc'])));
        $this->assertEqualsCanonicalizing(
            Package::published()->get()->filter(fn ($p) => in_array('kilimanjaro', $p->types ?? [], true) && $p->days <= 5)->pluck('title')->all(),
            $this->titles(route('tours', ['type' => ['kilimanjaro'], 'length' => ['1-5']])),
        );

        // 12+ days: the long safari-and-beach trips.
        $this->assertContains('Grand Tanzania', $this->titles(route('tours', ['length' => ['12-plus']])));

        // Nothing matches: friendly empty state.
        $this->get(route('tours', ['type' => ['kilimanjaro'], 'length' => ['12-plus']]))->assertSee('No trips match these filters.');

        // Unknown values are ignored.
        $this->assertCount(Package::published()->count(), $this->titles(route('tours', ['type' => ['nope'], 'length' => ['x'], 'sort' => 'bad'])));
    }

    public function test_sorting(): void
    {
        $priced = Package::published()->whereNotNull('price')->orderBy('price')->pluck('title')->all();

        $byPrice = $this->titles(route('tours', ['sort' => 'price-asc']));
        $this->assertSame($priced, array_slice($byPrice, 0, count($priced))); // priced first, cheapest first

        $byPriceDesc = $this->titles(route('tours', ['sort' => 'price-desc']));
        $this->assertSame(array_reverse($priced), array_slice($byPriceDesc, 0, count($priced)));

        $byDays = array_map(fn ($t) => Package::where('title', $t)->first()->days, $this->titles(route('tours', ['sort' => 'days-asc'])));
        $sorted = $byDays;
        sort($sorted);
        $this->assertSame($sorted, $byDays);
    }

    public function test_menu_links_to_the_tour_finder(): void
    {
        $this->get('/')->assertSee('Find a Tour')->assertSee(url('/tours'), false);
    }
}
