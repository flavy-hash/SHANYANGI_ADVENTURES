<?php

namespace Tests\Feature;

use App\Filament\Pages\EditSiteSettings;
use App\Filament\Resources\PageViews\Pages\ManagePageViews;
use App\Models\Package;
use App\Models\PageView;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VisitorLogTest extends TestCase
{
    use RefreshDatabase;

    protected const BROWSER = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    public function test_page_and_tour_views_are_recorded_anonymously(): void
    {
        $package = Package::query()->published()->firstOrFail();

        $this->withHeaders(['User-Agent' => self::BROWSER, 'Referer' => 'https://www.google.com/search?q=safari'])
            ->get('/')->assertOk();
        $this->withHeaders(['User-Agent' => self::BROWSER, 'Referer' => 'http://localhost/'])
            ->get(route('safaris.show', $package->slug))->assertOk();

        $this->assertSame(2, PageView::count());

        $home = PageView::where('page_type', 'home')->sole();
        $this->assertSame('/', $home->path);
        $this->assertSame('mobile', $home->device);
        $this->assertSame('google.com', $home->referrer_host);
        $this->assertSame(16, strlen($home->visitor_hash));
        $this->assertNotNull($home->created_at);

        $tour = PageView::where('page_type', 'package')->sole();
        $this->assertSame($package->id, $tour->package_id);
        $this->assertNull($tour->referrer_host); // internal navigation
        $this->assertSame($home->visitor_hash, $tour->visitor_hash); // same visitor, same day

        // Nothing identifying is stored.
        $stored = json_encode(PageView::all()->toArray());
        $this->assertStringNotContainsString('127.0.0.1', $stored);
        $this->assertStringNotContainsString('iPhone', $stored);
    }

    public function test_visitor_id_changes_every_day(): void
    {
        $this->withHeaders(['User-Agent' => self::BROWSER])->get('/');
        $this->travel(1)->days();
        $this->withHeaders(['User-Agent' => self::BROWSER])->get('/');

        $this->assertSame(2, PageView::distinct()->count('visitor_hash'));
    }

    public function test_private_and_non_page_requests_are_skipped(): void
    {
        $this->withHeaders(['User-Agent' => self::BROWSER, 'DNT' => '1'])->get('/');
        $this->withHeaders(['User-Agent' => self::BROWSER, 'Sec-GPC' => '1'])->get('/');
        $this->withHeaders(['User-Agent' => self::BROWSER, 'Sec-Purpose' => 'prefetch'])->get('/');
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)'])->get('/');
        $this->withHeaders(['User-Agent' => self::BROWSER])->get('/safaris/no-such-tour')->assertNotFound();
        $this->withHeaders(['User-Agent' => self::BROWSER])->post(route('newsletter.subscribe'), ['email' => 'a@example.com']);
        $this->actingAs(User::factory()->create())->withHeaders(['User-Agent' => self::BROWSER])->get('/');

        $this->assertSame(0, PageView::count());
    }

    public function test_logging_can_be_switched_off(): void
    {
        config(['analytics.enabled' => false]);
        $this->withHeaders(['User-Agent' => self::BROWSER])->get('/');

        $this->assertSame(0, PageView::count());
    }

    public function test_old_views_are_pruned(): void
    {
        PageView::create(['visitor_hash' => 'old', 'path' => '/', 'page_type' => 'home', 'device' => 'desktop', 'created_at' => now()->subDays(config('analytics.retention_days') + 1)]);
        PageView::create(['visitor_hash' => 'new', 'path' => '/', 'page_type' => 'home', 'device' => 'desktop', 'created_at' => now()->subDay()]);

        $this->artisan('model:prune', ['--model' => [PageView::class]])->assertSuccessful();

        $this->assertSame(['new'], PageView::pluck('visitor_hash')->all());
    }

    public function test_admin_sees_the_visitor_log(): void
    {
        $package = Package::query()->published()->firstOrFail();
        PageView::create(['visitor_hash' => 'abc', 'path' => '/safaris/'.$package->slug, 'page_type' => 'package', 'package_id' => $package->id, 'device' => 'mobile']);

        $this->actingAs(User::factory()->create());

        $this->get(ManagePageViews::getUrl())->assertOk()->assertSee($package->title);
        $this->get('/admin')->assertOk();
    }

    public function test_social_links_are_managed_in_site_settings(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(EditSiteSettings::class)
            ->fillForm(['social' => [
                'instagram' => 'https://www.instagram.com/shanyangi',
                'facebook' => 'https://www.facebook.com/shanyangi',
                'linkedin' => 'https://www.linkedin.com/company/shanyangi',
                'tiktok' => '',
                'youtube' => '',
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        auth()->logout();

        $this->get(route('contact'))
            ->assertSee('https://www.instagram.com/shanyangi', false)
            ->assertSee('https://www.facebook.com/shanyangi', false)
            ->assertSee('https://www.linkedin.com/company/shanyangi', false)
            ->assertDontSee('tiktok.com', false);

        Livewire::actingAs(User::factory()->create())
            ->test(EditSiteSettings::class)
            ->fillForm(['social' => ['instagram' => 'not a url']])
            ->call('save')
            ->assertHasFormErrors(['social.instagram']);
    }
}
