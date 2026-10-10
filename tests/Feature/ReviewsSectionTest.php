<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Machame Route' => 'Kilimanjaro', 'Zanzibar Beach Escape' => 'Zanzibar'] as $title => $category) {
            Package::create(['slug' => str($title)->slug(), 'title' => $title, 'category' => $category, 'summary' => 'Test package.']);
        }
    }

    /**
     * Replace all reviews with the given ones (array shape matches review()).
     */
    protected function setReviews(array $items): void
    {
        Review::query()->delete();

        foreach ($items as $item) {
            Review::create([
                'package_id' => Package::where('title', $item['package'] ?? null)->value('id'),
                'name' => $item['name'],
                'country' => $item['country'],
                'travelled_on' => $item['date'],
                'rating' => $item['rating'],
                'title' => $item['title'],
                'body' => $item['text'],
                'photos' => $item['images'],
                'is_published' => $item['published'] ?? true,
                'is_sample' => $item['sample'] ?? false,
            ]);
        }
    }

    protected function review(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'country' => 'Kenya',
            'date' => '2025-07-10',
            'rating' => 5,
            'title' => 'Great Trip',
            'text' => 'A real review.',
            'package' => 'Machame Route',
            'images' => [],
        ], $overrides);
    }

    public function test_sample_reviews_are_shown_outside_production(): void
    {
        $this->setReviews([$this->review(['sample' => true, 'name' => 'Sample Guest'])]);

        $this->get('/')->assertOk()->assertSee('Sample Guest')->assertSee('Read Review');
    }

    public function test_sample_reviews_are_never_shown_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $this->setReviews([
            $this->review(['sample' => true, 'name' => 'Sample Guest']),
            $this->review(['name' => 'Jane Doe']),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Sample Guest')
            ->assertSee('Jane Doe');
    }

    public function test_badges_still_render_when_there_are_no_reviews(): void
    {
        $this->setReviews([]);
        config(['reviews.google_reviews_url' => 'https://g.page/r/example']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Read More Reviews')
            ->assertSee('images/tripadvisor.png', false)
            ->assertSee('https://g.page/r/example', false);
    }

    public function test_badge_links_show_before_urls_are_configured(): void
    {
        config(['reviews.tripadvisor_url' => null, 'reviews.google_reviews_url' => null]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Read our reviews on Tripadvisor')
            ->assertSee('Read our reviews on Google')
            ->assertSee('href="#reviews"', false);
    }

    public function test_reviews_page_lists_reviews_with_a_rating_summary(): void
    {
        $this->setReviews([
            $this->review(['name' => 'Ann', 'rating' => 5, 'package' => 'Machame Route']),
            $this->review(['name' => 'Ben', 'rating' => 4, 'package' => 'Zanzibar Beach Escape']),
        ]);

        $this->get('/reviews')
            ->assertOk()
            ->assertSee('Ann')
            ->assertSee('Ben')
            ->assertSee('4.5')
            ->assertSee('Based on 2 reviews');
    }

    public function test_reviews_page_filters_by_trip_type(): void
    {
        $this->setReviews([
            $this->review(['name' => 'Climber Carl', 'package' => 'Machame Route']),
            $this->review(['name' => 'Beach Bea', 'package' => 'Zanzibar Beach Escape']),
        ]);

        $this->get('/reviews?type=kilimanjaro')
            ->assertOk()
            ->assertSee('Climber Carl')
            ->assertDontSee('Beach Bea');
    }

    public function test_reviews_page_never_shows_samples_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $this->setReviews([$this->review(['sample' => true, 'name' => 'Sample Guest'])]);

        $this->get('/reviews')
            ->assertOk()
            ->assertDontSee('Sample Guest')
            ->assertSee('Guest reviews will appear here soon');
    }

    public function test_home_shows_latest_three_and_links_to_the_reviews_page(): void
    {
        $this->setReviews(collect(range(1, 5))->map(fn ($i) => $this->review([
            'name' => "Guest {$i}",
            'date' => "2026-0{$i}-01",
        ]))->all());

        $this->get('/')
            ->assertOk()
            ->assertSee('Guest 5')
            ->assertSee('Guest 3')
            ->assertDontSee('Guest 2')
            ->assertSee('Read All 5 Reviews')
            ->assertSee(route('reviews'), false);
    }
}
