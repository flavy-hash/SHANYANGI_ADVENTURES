<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Package;
use App\Support\Seo;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected const MAIN = 'https://www.shanyangiadventures.co.tz';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
        config(['app.url' => self::MAIN, 'seo.canonical_redirect' => false]);
    }

    /** All JSON-LD blocks on a page, decoded. */
    protected function jsonLd(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        return array_map(function ($json) {
            $data = json_decode($json, true);
            $this->assertSame(JSON_ERROR_NONE, json_last_error(), 'invalid JSON-LD');

            return $data;
        }, $m[1]);
    }

    public function test_canonical_and_og_url_use_app_url_even_on_another_host(): void
    {
        foreach (['http://shanyangiadventures.co.tz', 'https://shanyangiadventureandsafaris.mileletechnologies.com'] as $host) {
            $this->get($host.'/safaris/machame-route?utm_source=x&type=y')
                ->assertOk()
                ->assertSee('<link rel="canonical" href="'.self::MAIN.'/safaris/machame-route">', false)
                ->assertSee('<meta property="og:url" content="'.self::MAIN.'/safaris/machame-route">', false)
                ->assertDontSee('utm_source=x', false);
        }

        $this->get('http://shanyangiadventures.co.tz/')->assertSee('<link rel="canonical" href="'.self::MAIN.'/">', false);
    }

    public function test_every_page_has_description_open_graph_and_twitter_tags_with_absolute_urls(): void
    {
        foreach (['/', '/safaris', '/about-us', '/contact-us', '/blog', '/safaris/machame-route'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            foreach (['name="description"', 'property="og:title"', 'property="og:description"', 'property="og:image"', 'name="twitter:card"', 'name="twitter:image"'] as $tag) {
                $this->assertSame(1, substr_count($html, $tag), "$path: $tag");
            }
            $this->assertMatchesRegularExpression('#property="og:image" content="https://[^"]+"#', $html, "$path og:image");
            $this->assertDoesNotMatchRegularExpression('#<meta name="description" content="\s*"#', $html, "$path empty description");
        }
    }

    public function test_titles_name_the_brand_exactly_once(): void
    {
        $this->assertSame('Shanyangi Adventures | Tanzania Safaris & Tours', Seo::title('Shanyangi Adventures | Tanzania Safaris &amp; Tours'));
        $this->assertSame('About Us — Shanyangi Adventures', Seo::title('About Us'));
        $this->assertSame('Shanyangi Adventures', Seo::title(''));

        $paths = ['/', '/safaris', '/tours', '/activities', '/accommodations', '/blog', '/reviews', '/about-us', '/contact-us', '/privacy-policy', '/safaris/machame-route'];
        $paths[] = route('blog.show', BlogPost::query()->published()->value('slug'), false);

        $titles = [];
        foreach ($paths as $path) {
            preg_match('#<title>(.*?)</title>#s', $this->get($path)->assertOk()->getContent(), $m);
            $title = html_entity_decode($m[1]);

            $this->assertSame(1, substr_count($title, 'Shanyangi Adventures'), "$path: $title");
            $this->assertStringNotContainsString('| Shanyangi Adventures', $title, $path);
            $titles[] = $title;
        }

        $this->assertSame('Shanyangi Adventures | Tanzania Safaris & Tours', $titles[0]); // home keeps its tagline
        $this->assertSame('About Us — Shanyangi Adventures', $titles[7]);
        $this->assertSame(count($titles), count(array_unique($titles)), 'titles must be unique');
    }

    public function test_business_json_ld_is_on_every_page(): void
    {
        config(['site.email' => 'info@example.com', 'site.phone' => '+255 787 156 775', 'site.address' => 'Arusha, Tanzania']);

        $graph = $this->jsonLd($this->get('/about-us')->getContent())[0]['@graph'];
        $business = collect($graph)->firstWhere('@type', 'TravelAgency');

        $this->assertSame('Shanyangi Adventures', $business['name']);
        $this->assertSame(self::MAIN.'/', $business['url']);
        $this->assertSame(self::MAIN.'/images/logo/shanyangi-logo.png', $business['logo']);
        $this->assertSame('+255 787 156 775', $business['telephone']);
        $this->assertSame('info@example.com', $business['email']);
        $this->assertSame('Arusha, Tanzania', $business['address']['streetAddress']);
        $this->assertNotNull(collect($graph)->firstWhere('@type', 'WebSite'));
    }

    public function test_package_page_has_tourist_trip_json_ld(): void
    {
        $package = Package::query()->published()->where('price', '>', 0)->firstOrFail();

        $trip = collect($this->jsonLd($this->get(route('safaris.show', $package->slug))->assertOk()->getContent()))
            ->firstWhere('@type', 'TouristTrip');

        $this->assertNotNull($trip);
        $this->assertSame($package->title, $trip['name']);
        $this->assertSame(self::MAIN.'/safaris/'.$package->slug, $trip['url']);
        $this->assertStringStartsWith('https://', $trip['image']); // public photo URL, else the default share image
        $this->assertSame(self::MAIN.'/#organization', $trip['provider']['@id']);
        $this->assertSame(max($package->days, count($package->itinerary)), $trip['itinerary']['numberOfItems']);
        $this->assertSame('Offer', $trip['offers']['@type']);
        $this->assertEquals($package->fresh()->price, $trip['offers']['price']);
        $this->assertSame('USD', $trip['offers']['priceCurrency']);

        // No price yet ("on request"): no offer rather than a price of 0.
        $package->update(['price' => null, 'price_tiers' => null]);
        $trip = collect($this->jsonLd($this->get(route('safaris.show', $package->slug))->getContent()))->firstWhere('@type', 'TouristTrip');
        $this->assertArrayNotHasKey('offers', $trip);
    }

    public function test_blog_article_has_blog_posting_json_ld(): void
    {
        $post = BlogPost::query()->published()->firstOrFail();

        $article = collect($this->jsonLd($this->get(route('blog.show', $post->slug))->getContent()))->firstWhere('@type', 'BlogPosting');

        $this->assertSame(self::MAIN.'/blog/'.$post->slug, $article['url']);
        $this->assertSame($post->published_at->toDateString(), $article['datePublished']);
    }

    public function test_protected_photos_fall_back_to_the_public_share_image(): void
    {
        // Protected media only opens inside a visitor's session, so crawlers get the default image.
        $this->assertSame(self::MAIN.'/images/shanyangi.jpeg', Seo::image('seed/photo.jpg'));
        $this->assertSame(self::MAIN.'/images/shanyangi.jpeg', Seo::image(null));
        $this->assertSame('https://cdn.example.com/a.jpg', Seo::image('https://cdn.example.com/a.jpg'));
        $this->assertFileExists(public_path(config('seo.default_image')));
        $this->assertFileExists(public_path(config('seo.logo')));
    }

    public function test_json_ld_text_cannot_close_the_script_tag(): void
    {
        $this->assertStringNotContainsString('</script>', Seo::json(['name' => 'Evil </script><script>alert(1)</script>']));
    }

    public function test_verification_tags_appear_only_when_configured(): void
    {
        config(['seo.google_verification' => null, 'seo.bing_verification' => null]);
        $this->get('/')->assertDontSee('google-site-verification', false)->assertDontSee('msvalidate.01', false);

        config(['seo.google_verification' => 'abc123-Google_token', 'seo.bing_verification' => 'BING0123456789']);
        $this->get('/safaris')
            ->assertSee('<meta name="google-site-verification" content="abc123-Google_token">', false)
            ->assertSee('<meta name="msvalidate.01" content="BING0123456789">', false);
    }

    public function test_sitemap_is_valid_xml_with_every_public_page_on_app_url_only(): void
    {
        $response = $this->get('http://old-domain.example/sitemap.xml')->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap is not valid XML');

        $locs = array_map('strval', iterator_to_array($xml->xpath('//*[local-name()="loc"]'), false));
        $this->assertSame(count($locs), count(array_unique($locs)), 'duplicate URLs');

        foreach ($locs as $loc) {
            $this->assertStringStartsWith(self::MAIN.'/', $loc);
            $this->assertStringNotContainsString('?', $loc);
            $this->assertDoesNotMatchRegularExpression('#/(admin|livewire|media|quotes|chat|login)(/|$)#', $loc);
        }

        $this->assertContains(self::MAIN.'/', $locs);
        $this->assertContains(self::MAIN.'/contact-us', $locs);
        foreach (Package::query()->published()->pluck('slug') as $slug) {
            $this->assertContains(self::MAIN.'/safaris/'.$slug, $locs);
        }
        foreach (BlogPost::query()->published()->pluck('slug') as $slug) {
            $this->assertContains(self::MAIN.'/blog/'.$slug, $locs);
        }

        // Unpublished pages are left out; detail pages carry their last change.
        $hidden = Package::query()->published()->firstOrFail();
        $hidden->update(['is_published' => false]);
        $this->assertStringNotContainsString(self::MAIN.'/safaris/'.$hidden->slug.'<', $this->get('/sitemap.xml')->getContent());
        $this->assertStringContainsString('<lastmod>', $response->getContent());
    }

    public function test_robots_txt_blocks_admin_and_lists_the_sitemap(): void
    {
        $this->assertFileDoesNotExist(public_path('robots.txt')); // would be served instead of the route

        $body = $this->get('http://old-domain.example/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->getContent();

        $this->assertStringContainsString("User-agent: *\n", $body);
        $this->assertStringContainsString("Disallow: /admin\n", $body);
        $this->assertStringNotContainsString("Disallow: /\n", $body);
        $this->assertStringEndsWith('Sitemap: '.self::MAIN."/sitemap.xml\n", $body);
    }

    public function test_other_hosts_are_301_redirected_when_turned_on(): void
    {
        config(['seo.canonical_redirect' => true]);

        $this->get('http://shanyangiadventures.co.tz/safaris?type=honeymoon')
            ->assertStatus(301)
            ->assertRedirect(self::MAIN.'/safaris?type=honeymoon');

        $this->get('https://shanyangiadventureandsafaris.mileletechnologies.com/')->assertStatus(301)->assertRedirect(self::MAIN.'/');
        $this->call('HEAD', 'http://shanyangiadventures.co.tz/about-us')->assertStatus(301);

        // Already on the main host: no redirect.
        $this->get(self::MAIN.'/safaris')->assertOk();
    }

    public function test_post_requests_are_never_redirected(): void
    {
        config(['seo.canonical_redirect' => true]);

        $response = $this->post('http://shanyangiadventures.co.tz/contact-us', ['name' => 'Jane', 'email' => 'jane@example.com', 'adults' => 2]);

        $this->assertNotSame(301, $response->getStatusCode());
        $this->assertSame(1, \App\Models\TripRequest::count());
    }

    public function test_redirect_is_off_by_default_and_ignores_a_local_app_url(): void
    {
        $this->get('http://shanyangiadventures.co.tz/safaris')->assertOk(); // CANONICAL_REDIRECT=false

        config(['seo.canonical_redirect' => true, 'app.url' => 'http://localhost']);
        $this->get('http://shanyangiadventures.co.tz/safaris')->assertOk(); // never send live visitors to localhost
    }
}
