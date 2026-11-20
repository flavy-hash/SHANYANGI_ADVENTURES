<?php

namespace Tests\Feature;

use App\Models\Package;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafariFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    /** Package card titles on a /safaris response. */
    protected function titles(array $query = []): array
    {
        $html = $this->get(route('safaris', $query))->assertOk()->getContent();
        preg_match_all('#<h3 class="tm-package-title">\s*(.*?)\s*</h3>#s', $html, $m);

        return array_map(fn ($t) => trim(html_entity_decode(strip_tags($t))), $m[1]);
    }

    protected function published(callable $filter): array
    {
        return Package::query()->published()->ordered()->get()->filter($filter)->pluck('title')->values()->all();
    }

    public function test_panel_shows_destination_style_and_duration_rows(): void
    {
        $this->get(route('safaris'))
            ->assertOk()
            ->assertSee('Destination')->assertSee('Northern Circuit')->assertSee('Kilimanjaro')
            ->assertSee('Style')->assertSee('Budget')->assertSee('Mid-range')->assertSee('Luxury')
            ->assertSee('Duration')->assertSee('Any')->assertSee('12 days');

        $this->assertCount(Package::published()->count(), $this->titles());
    }

    public function test_each_row_filters_and_rows_combine(): void
    {
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => in_array('kilimanjaro', $p->destinations, true)),
            $this->titles(['destination' => 'kilimanjaro']),
        );

        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => $p->style === 'luxury'),
            $this->titles(['style' => 'luxury']),
        );

        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => $p->days === 7),
            $this->titles(['days' => '7']),
        );

        // Destination + style + duration together.
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => in_array('northern-circuit', $p->destinations, true) && $p->style === 'mid-range' && $p->days === 7),
            $this->titles(['destination' => 'northern-circuit', 'style' => 'mid-range', 'days' => '7']),
        );

        // Menu links (?type=) still work, alongside the panel.
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => in_array('honeymoon', $p->types ?? [], true)),
            $this->titles(['type' => 'honeymoon']),
        );
    }

    public function test_chips_keep_the_other_rows_and_mark_the_active_choice(): void
    {
        $html = $this->get(route('safaris', ['destination' => 'zanzibar']))->getContent();

        // A style chip keeps the chosen destination.
        $this->assertStringContainsString(e(route('safaris', ['destination' => 'zanzibar', 'style' => 'luxury'])), $html);
        // The active chip is marked.
        $this->assertMatchesRegularExpression('#class="tm-filter-chip is-active"[^>]*>Zanzibar<#', $html);
        $this->assertStringContainsString('Clear filters', $html);
    }

    public function test_no_matches_and_unknown_values(): void
    {
        $this->get(route('safaris', ['destination' => 'kilimanjaro', 'days' => '3']))
            ->assertSee('No packages match these filters.');

        $this->assertCount(Package::published()->count(), $this->titles(['destination' => 'mars', 'style' => 'x', 'days' => '99']));
    }

    public function test_price_slider_limits_the_price_per_person(): void
    {
        $prices = Package::published()->whereNotNull('price')->where('price', '>', 0)->pluck('price');
        $min = (int) (floor($prices->min() / 10) * 10);
        $max = (int) (ceil($prices->max() / 10) * 10);

        // The slider runs from the cheapest to the dearest trip, starting at "no limit".
        $this->get(route('safaris'))
            ->assertOk()
            ->assertSee('Tour price')
            ->assertSee('min="'.$min.'" max="'.$max.'" step="10" value="'.$max.'"', false)
            ->assertSee('$'.number_format($min))
            ->assertSee('$'.number_format($max));

        // A limit shows only priced trips at or under it ("on request" trips have no price to compare).
        $limit = 2500;
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => $p->price && $p->price <= $limit),
            $this->titles(['price_max' => $limit]),
        );
        $this->get(route('safaris', ['price_max' => $limit]))
            ->assertSee('Up to $2,500')
            ->assertSee('value="2500"', false)
            ->assertSee('Clear filters');

        // It combines with the other rows, and their chips keep the limit.
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => $p->style === 'mid-range' && $p->price && $p->price <= $limit),
            $this->titles(['style' => 'mid-range', 'price_max' => $limit]),
        );
        $html = $this->get(route('safaris', ['style' => 'mid-range', 'price_max' => $limit]))->getContent();
        $this->assertStringContainsString(e(route('safaris', ['style' => 'luxury', 'price_max' => '2500'])), $html);
        // The slider form carries the other filters along.
        $this->assertStringContainsString('<input type="hidden" name="style" value="mid-range">', $html);

        // The top of the range, or nonsense, means no limit.
        $this->assertCount(Package::published()->count(), $this->titles(['price_max' => $max]));
        $this->assertCount(Package::published()->count(), $this->titles(['price_max' => 'cheap']));
        // Below the cheapest trip it clamps to the cheapest price.
        $this->assertEqualsCanonicalizing(
            $this->published(fn ($p) => $p->price && $p->price <= $min),
            $this->titles(['price_max' => 1]),
        );
    }

    public function test_footer_shows_association_logos_and_payment_cards(): void
    {
        $this->get('/')
            ->assertSee('Tanzania Association of Tour Operators (TATO)')
            ->assertSee('African Travel &amp; Tourism Association (ATTA)', false)
            ->assertSee('We Accept')
            ->assertSee('images/payment-cards.webp', false);
    }
}
