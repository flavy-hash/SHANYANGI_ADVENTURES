<?php

namespace Tests\Feature;

use App\Filament\Pages\EditAboutPage;
use App\Filament\Resources\Activities\Pages\ManageActivities;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Resources\NewsletterSubscribers\Pages\ManageNewsletterSubscribers;
use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\TripRequests\Pages\ListTripRequests;
use App\Filament\Resources\TripRequests\Pages\ViewTripRequest;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\NewsletterSubscriber;
use App\Models\Package;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\TripRequest;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    protected function admin(): User
    {
        return User::factory()->create();
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/packages')->assertRedirect('/admin/login');
    }

    public function test_every_admin_screen_loads(): void
    {
        $this->actingAs($this->admin());

        $tripRequest = TripRequest::create(['name' => 'Jane', 'email' => 'jane@example.com', 'adults' => 2, 'children' => 0]);

        foreach ([
            '/admin',
            '/admin/trip-requests',
            '/admin/trip-requests/'.$tripRequest->id,
            '/admin/newsletter-subscribers',
            '/admin/packages',
            '/admin/packages/create',
            '/admin/packages/'.Package::first()->getRouteKey().'/edit',
            '/admin/activities',
            '/admin/blog-posts',
            '/admin/blog-posts/create',
            '/admin/blog-posts/'.BlogPost::first()->getRouteKey().'/edit',
            '/admin/reviews',
            '/admin/reviews/'.Review::first()->id.'/edit',
            '/admin/about-page',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_lists_show_seeded_content(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(ListPackages::class)->assertCanSeeTableRecords(Package::all());
        Livewire::test(ListBlogPosts::class)->assertCanSeeTableRecords(BlogPost::all());
        Livewire::test(ListReviews::class)->assertCanSeeTableRecords(Review::all());
        Livewire::test(ManageActivities::class)->assertCanSeeTableRecords(Activity::all());
    }

    public function test_create_a_package_with_an_itinerary_and_it_appears_on_the_site(): void
    {
        $this->actingAs($this->admin());
        $undoRepeaterFake = Repeater::fake(); // predictable repeater keys for fillForm()

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'title' => 'Tarangire Day Safari',
                'slug' => 'tarangire-day-safari',
                'category' => 'Safari',
                'summary' => 'A full day among Tarangire\'s elephants.',
                'price' => 350,
                'is_published' => true,
                'itinerary' => [
                    ['title' => 'Into Tarangire', 'text' => 'Game drive among the baobabs.', 'meals' => 'Lunch'],
                ],
                'included' => [['item' => 'Park fees']], // simple repeater's internal shape; saved as ['Park fees']
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Package::where('slug', 'tarangire-day-safari')->firstOrFail();
        $this->assertSame('Into Tarangire', $package->itinerary[0]['title']);
        $this->assertSame(['Park fees'], $package->included);

        $this->get('/safaris/tarangire-day-safari')
            ->assertOk()
            ->assertSee('Tarangire Day Safari')
            ->assertSee('Into Tarangire')
            ->assertSee('Park fees');

        $undoRepeaterFake();
    }

    public function test_package_requires_title_summary_and_unique_slug(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePackage::class)
            ->fillForm(['title' => '', 'slug' => 'machame-route', 'category' => 'Safari', 'summary' => ''])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'summary' => 'required', 'slug' => 'unique']);
    }

    public function test_editing_a_package_updates_the_website(): void
    {
        $this->actingAs($this->admin());
        $package = Package::where('slug', 'machame-route')->firstOrFail();

        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->fillForm(['tagline' => 'Edited in the admin'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/safaris/machame-route')->assertSee('Machame Route');
        $this->get('/safaris')->assertSee('Edited in the admin');
    }

    public function test_blog_post_body_headings_render(): void
    {
        $this->actingAs($this->admin());
        $post = BlogPost::first();

        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['body' => "Intro paragraph.\n\n## A new heading\nUnder the heading."])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/blog/'.$post->slug)
            ->assertSee('<h2>A new heading</h2>', false)
            ->assertSee('<p>Under the heading.</p>', false);
    }

    public function test_new_blog_post_can_be_scheduled(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'Coming Soon Post',
                'slug' => 'coming-soon-post',
                'category' => 'safari',
                'published_at' => now()->addWeek()->toDateString(),
                'read_minutes' => 3,
                'excerpt' => 'Soon.',
                'body' => 'Body.',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/blog/coming-soon-post')->assertNotFound();
    }

    public function test_unpublishing_a_review_hides_it(): void
    {
        $this->actingAs($this->admin());
        $review = Review::first();

        Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
            ->fillForm(['is_published' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/reviews')->assertDontSee($review->title);
    }

    public function test_about_page_can_be_edited(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(EditAboutPage::class)
            ->fillForm([
                'story_title' => 'Three Generations of Guides',
                'story' => "Paragraph one.\n\nParagraph two.",
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Three Generations of Guides', SiteSetting::get('about')['story_title']);
        $this->get('/about-us')->assertSee('Three Generations of Guides')->assertSee('Paragraph two.');
    }

    public function test_inquiry_can_be_marked_handled(): void
    {
        $this->actingAs($this->admin());
        $tripRequest = TripRequest::create(['name' => 'Jane', 'email' => 'jane@example.com', 'adults' => 2, 'children' => 0]);

        Livewire::test(ListTripRequests::class)
            ->assertCanSeeTableRecords([$tripRequest])
            ->callTableAction('toggleHandled', $tripRequest);

        $this->assertNotNull($tripRequest->fresh()->handled_at);

        Livewire::test(ViewTripRequest::class, ['record' => $tripRequest->getRouteKey()])
            ->callAction('toggleHandled');

        $this->assertNull($tripRequest->fresh()->handled_at);
    }

    public function test_inquiry_list_has_the_actions_menu(): void
    {
        $this->actingAs($this->admin());
        $tripRequest = TripRequest::create(['name' => 'Jane', 'email' => 'jane@example.com', 'adults' => 2, 'children' => 0]);

        Livewire::test(ListTripRequests::class)
            ->assertTableActionVisible('view', $tripRequest)
            ->assertTableActionHasUrl('reply', 'mailto:jane@example.com?subject='.rawurlencode('Your trip request: Tanzania'), $tripRequest)
            ->assertTableActionHasUrl('quote', CreateQuote::getUrl(['trip_request' => $tripRequest->id]), $tripRequest)
            ->callTableAction('delete', $tripRequest);

        $this->assertModelMissing($tripRequest);
    }

    public function test_newsletter_export_contains_only_active_subscribers(): void
    {
        $this->actingAs($this->admin());
        NewsletterSubscriber::create(['email' => 'active@example.com', 'source' => 'footer']);
        NewsletterSubscriber::create(['email' => 'gone@example.com', 'source' => 'footer', 'unsubscribed_at' => now()]);

        $active = NewsletterSubscriber::where('email', 'active@example.com')->first();
        $csv = fopen('php://memory', 'r+');
        fputcsv($csv, ['email', 'source', 'subscribed_at']);
        fputcsv($csv, [$active->email, $active->source, $active->created_at->toDateTimeString()]);
        rewind($csv);

        Livewire::test(ManageNewsletterSubscribers::class)
            ->callAction('export')
            ->assertFileDownloaded('newsletter-subscribers-'.now()->format('Y-m-d').'.csv', stream_get_contents($csv));
    }
}
