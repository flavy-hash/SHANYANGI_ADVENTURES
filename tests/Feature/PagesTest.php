<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Package;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Starter content (images stay as remote URLs in tests: SEED_DOWNLOAD_IMAGES=false).
        $this->seed(ContentSeeder::class);
    }

    public function test_every_navigation_page_loads(): void
    {
        foreach (['/', '/safaris', '/activities', '/accommodations', '/blog', '/about-us', '/contact-us'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_safari_filter_shows_only_matching_packages(): void
    {
        $this->get('/safaris?type=honeymoon')
            ->assertOk()
            ->assertSee('Safari &amp; Sea Honeymoon', false)
            ->assertDontSee('Lemosho Route')
            ->assertSee('aria-current="page">Honeymoon<', false);
    }

    public function test_unknown_filter_values_show_everything(): void
    {
        $this->get('/safaris?type=nonsense')
            ->assertOk()
            ->assertSee('Lemosho Route')
            ->assertSee('Machame Route');
    }

    public function test_activity_and_stay_location_filters(): void
    {
        // Count cards rather than text: the header mega menu mentions places on every page.
        $zanzibar = Activity::where('location', 'zanzibar')->count();
        $html = $this->get('/activities?location=zanzibar')->assertOk()->assertSee('Snorkelling Excursion')->getContent();
        $this->assertSame($zanzibar, substr_count($html, 'class="tm-info-card"'));

        $karatu = collect(config('accommodations.items'))->where('location', 'karatu')->count();
        $html = $this->get('/accommodations?location=karatu')->assertOk()->assertSee('Karatu Coffee Farm Lodge')->getContent();
        $this->assertSame($karatu, substr_count($html, 'class="tm-info-card"'));
        $this->assertStringNotContainsString('Zanzibar Beachfront Resort', $html);
    }

    public function test_package_cards_link_to_their_package_page(): void
    {
        $this->get('/safaris')
            ->assertSee('href="'.route('safaris.show', 'machame-route').'"', false)
            ->assertSee('Read more');
    }

    public function test_every_package_page_shows_its_full_itinerary(): void
    {
        foreach (Package::all() as $package) {
            $days = count($package->itinerary ?? []);
            $this->assertGreaterThan(0, $days, "{$package->slug} has no itinerary");

            $html = $this->get(route('safaris.show', $package->slug))
                ->assertOk()
                ->assertSee('Package Overview')
                ->assertSee('Day-by-Day Itinerary')
                ->getContent();

            $this->assertSame($days, substr_count($html, 'class="tm-day"'), "{$package->slug} day count");
        }
    }

    public function test_package_page_booking_opens_a_prefilled_enquiry(): void
    {
        $this->get('/safaris/machame-route')
            ->assertSee('Uhuru Peak')
            ->assertSee(route('contact', ['trip' => 'Machame Route']).'#request', false);

        $this->get('/contact-us?trip=Machame+Route')
            ->assertOk()
            ->assertSee('value="Machame Route"', false);
    }

    public function test_unknown_package_is_not_found(): void
    {
        $this->get('/safaris/no-such-trip')->assertNotFound();
    }

    public function test_blog_listing_categories_and_articles(): void
    {
        $this->get('/blog/category/climbing')
            ->assertOk()
            ->assertSee('Machame vs Lemosho')
            ->assertDontSee('What to Pack for Your First Safari');

        $this->get('/blog/what-to-pack-for-your-first-safari')
            ->assertOk()
            ->assertSee('What to Pack for Your First Safari')
            ->assertSee('More From the Blog');

        $this->get('/blog/category/unknown')->assertNotFound();
        $this->get('/blog/no-such-article')->assertNotFound();
    }

    public function test_about_page_has_the_anchors_used_in_the_menu(): void
    {
        $this->get('/about-us')
            ->assertOk()
            ->assertSee('id="story"', false)
            ->assertSee('id="team"', false)
            ->assertSee('id="reviews"', false);
    }

    public function test_unpublished_content_is_hidden(): void
    {
        Package::where('slug', 'lemosho-route')->update(['is_published' => false]);
        BlogPost::where('slug', 'what-to-pack-for-your-first-safari')->update(['published_at' => now()->addWeek()]);
        Activity::where('title', 'Snorkelling Excursion')->update(['is_published' => false]);

        $this->get('/safaris')->assertOk()->assertDontSee(route('safaris.show', 'lemosho-route'), false);
        $this->get('/safaris/lemosho-route')->assertNotFound();
        $this->get('/blog/what-to-pack-for-your-first-safari')->assertNotFound();
        $this->get('/activities')->assertOk()->assertDontSee('Snorkelling Excursion');
    }

    public function test_about_page_shows_admin_content(): void
    {
        \App\Models\SiteSetting::put('about', [
            'story_title' => 'Our Family Story',
            'story' => "First paragraph about us.

Second paragraph.",
            'team' => [['icon' => 'heart', 'title' => 'Head Chef', 'text' => 'Cooks for every safari.']],
        ]);

        $this->get('/about-us')
            ->assertOk()
            ->assertSee('Our Family Story')
            ->assertSee('Second paragraph.')
            ->assertSee('Head Chef')
            ->assertSee('About Shanyangi Adventures'); // default kept for fields not set
    }

    public function test_current_page_is_highlighted_in_the_menu(): void
    {
        $this->get('/activities')->assertSee('tm-nav-active', false);
    }
}
