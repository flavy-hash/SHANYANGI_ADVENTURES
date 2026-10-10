<?php

namespace Tests\Feature;

use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use App\Support\UploadedImage;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WriteReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    protected function form(array $overrides = []): array
    {
        return [
            'rating' => 5,
            'name' => 'Amelia R.',
            'email' => 'amelia@example.com',
            'country' => 'Australia',
            'package_id' => Package::query()->published()->value('id'),
            'travelled_on' => now()->subMonths(2)->format('Y-m'),
            'title' => 'Lions at sunrise',
            'body' => 'Our guide Joseph found us a pride of lions on the first morning. Unforgettable!',
            ...$overrides,
        ];
    }

    public function test_reviews_page_has_a_write_a_review_button_and_form(): void
    {
        $this->get(route('reviews'))
            ->assertOk()
            ->assertSee('Write a Review')
            ->assertSee('data-dialog-open="write-review"', false)
            ->assertSee(route('reviews.store'), false)
            ->assertSee('name="rating"', false);
    }

    public function test_a_review_waits_for_approval_and_the_team_is_alerted(): void
    {
        $admin = User::factory()->create();

        $this->post(route('reviews.store'), $this->form())
            ->assertRedirect(route('reviews').'#write-review')
            ->assertSessionHas('review_sent', 'Amelia');

        $review = Review::where('title', 'Lions at sunrise')->sole();
        $this->assertFalse($review->is_published);
        $this->assertSame('website', $review->source);
        $this->assertSame('amelia@example.com', $review->email);
        $this->assertSame(now()->subMonths(2)->startOfMonth()->toDateString(), $review->travelled_on->toDateString());
        $this->assertSame(1, $admin->notifications()->count());

        // Not on the website yet; the thank-you message is.
        $this->get(route('reviews'))
            ->assertSee('Your review will appear on this page once our team has checked it')
            ->assertDontSee('Our guide Joseph found us a pride of lions');
    }

    public function test_review_form_is_validated(): void
    {
        $this->post(route('reviews.store'), $this->form([
            'rating' => null,
            'email' => 'not-an-email',
            'travelled_on' => now()->addMonths(2)->format('Y-m'),
            'body' => 'Too short',
            'package_id' => 999999,
        ]))
            ->assertRedirect(route('reviews').'#write-review')
            ->assertSessionHasErrorsIn('review', ['rating', 'email', 'travelled_on', 'body', 'package_id']);

        $this->post(route('reviews.store'), $this->form(['website' => 'http://spam.example']))
            ->assertSessionHas('review_sent');

        $this->assertSame(0, Review::where('source', 'website')->count());
    }

    public function test_reviewers_can_add_photos_which_are_resized_and_cleaned(): void
    {
        Storage::fake('media');

        $this->post(route('reviews.store'), $this->form([
            'photos' => [
                UploadedFile::fake()->image('serengeti.jpg', 4000, 3000),
                UploadedFile::fake()->image('crater.png', 800, 600),
            ],
        ]))->assertSessionHas('review_sent');

        $photos = Review::where('source', 'website')->sole()->photos;
        $this->assertCount(2, $photos);

        foreach ($photos as $path) {
            $this->assertMatchesRegularExpression('#^uploads/reviews/[0-9a-f-]{36}\.jpg$#', $path);
            Storage::disk('media')->assertExists($path);
            $size = getimagesizefromstring(Storage::disk('media')->get($path));
            $this->assertSame(IMAGETYPE_JPEG, $size[2]); // PNG converted too
            $this->assertLessThanOrEqual(1600, max($size[0], $size[1]));
        }

        // Deleting the review (e.g. spam) deletes its photos.
        Review::where('source', 'website')->sole()->delete();
        $this->assertSame([], Storage::disk('media')->allFiles());
    }

    public function test_photo_limits_are_enforced(): void
    {
        Storage::fake('media');

        $this->post(route('reviews.store'), $this->form([
            'photos' => array_map(fn ($i) => UploadedFile::fake()->image("p$i.jpg"), range(1, 5)),
        ]))->assertSessionHasErrorsIn('review', ['photos']);

        $this->post(route('reviews.store'), $this->form([
            'photos' => [UploadedFile::fake()->image('huge.jpg')->size(9000)],
        ]))->assertSessionHasErrorsIn('review', ['photos.0']);

        $this->assertSame(0, Review::where('source', 'website')->count());
        $this->assertSame([], Storage::disk('media')->allFiles());
    }

    public function test_files_that_are_not_really_images_are_never_stored(): void
    {
        Storage::fake('media');

        // Even if a disguised file got past validation, re-encoding refuses it.
        $this->assertNull(UploadedImage::store(UploadedFile::fake()->createWithContent('shell.jpg', '<?php echo "hi"; ?>'), 'uploads/reviews'));
        $this->assertSame([], Storage::disk('media')->allFiles());
    }

    public function test_approved_review_photos_show_on_the_review_card(): void
    {
        $this->post(route('reviews.store'), $this->form([
            'photos' => [UploadedFile::fake()->image('lions.jpg', 1200, 800)],
        ]));
        $review = Review::where('source', 'website')->sole();
        $path = $review->photos[0];

        try {
            $review->update(['is_published' => true]);
            $this->get(route('reviews'))
                ->assertOk()
                ->assertSee('/media/image/'.$path, false); // signed, protected URL
        } finally {
            Storage::disk('media')->delete($path);
        }
    }

    public function test_admin_approves_a_review_and_it_goes_live(): void
    {
        $this->post(route('reviews.store'), $this->form());
        $review = Review::where('source', 'website')->sole();

        $this->actingAs(User::factory()->create());
        $this->assertSame('1', ReviewResource::getNavigationBadge());

        Livewire::test(ListReviews::class)
            ->filterTable('awaiting_approval')
            ->assertCanSeeTableRecords([$review])
            ->assertSee('Awaiting approval');

        Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
            ->assertSee('amelia@example.com')
            ->callAction('approve')
            ->assertActionHidden('approve');

        $this->assertTrue($review->fresh()->is_published);
        $this->assertNull(ReviewResource::getNavigationBadge());

        auth()->logout();
        $this->get(route('reviews'))
            ->assertSee('Our guide Joseph found us a pride of lions')
            ->assertDontSee('amelia@example.com'); // the email stays private
    }
}
