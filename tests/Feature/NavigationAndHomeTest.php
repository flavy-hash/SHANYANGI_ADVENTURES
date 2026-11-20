<?php

namespace Tests\Feature;

use App\Filament\Pages\EditHomePage;
use App\Filament\Pages\EditNavigation;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\HomePage;
use App\Support\Navigation;
use App\Support\ProtectedMedia;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class NavigationAndHomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
        $this->actingAs(User::factory()->create());
    }

    public function test_menu_defaults_to_the_config_and_round_trips_through_the_form(): void
    {
        $this->assertSame(config('navigation.primary'), Navigation::primary());

        // Online photos are kept when the form is saved without uploading new ones.
        $this->assertSame(Navigation::defaults(), Navigation::fromForm(Navigation::toForm(Navigation::defaults())));
    }

    public function test_admin_edits_the_menu_and_the_website_follows(): void
    {
        $component = Livewire::test(EditNavigation::class)->assertOk()->assertSee('Menu items');

        $items = $component->get('data.items');
        $first = array_key_first($items);
        $items[$first]['label'] = 'Our Safaris';
        // A new plain menu item without a dropdown.
        $items['new'] = ['label' => 'Kilimanjaro', 'path' => '/safaris?destination=kilimanjaro', 'children' => [], 'mega' => []];

        $component->set('data.items', $items)->call('save')->assertHasNoFormErrors();

        $menu = Navigation::primary();
        $this->assertSame('Our Safaris', $menu[0]['label']);
        $this->assertSame('Kilimanjaro', end($menu)['label']);
        $this->assertArrayNotHasKey('children', end($menu));
        $this->assertStringStartsWith('https://', $menu[0]['mega']['image']); // online photo kept

        $this->get('/')
            ->assertOk()
            ->assertSee('Our Safaris')
            ->assertSee('href="'.url('/safaris?destination=kilimanjaro').'"', false);

        // Bad links are refused.
        $items['new']['path'] = 'javascript:alert(1)';
        Livewire::test(EditNavigation::class)->set('data.items', $items)->call('save')->assertHasFormErrors();

        // Restore brings the original menu back.
        Livewire::test(EditNavigation::class)->callAction('restore');
        $this->assertSame(config('navigation.primary'), Navigation::primary());
    }

    public function test_uploaded_menu_photos_are_served_through_protected_media(): void
    {
        $menu = Navigation::defaults();
        $menu[0]['mega']['image'] = 'uploads/navigation/panel.jpg';
        File::ensureDirectoryExists(ProtectedMedia::root('images/uploads/navigation'));
        File::put(ProtectedMedia::root('images/uploads/navigation/panel.jpg'), 'jpg');
        SiteSetting::put(Navigation::KEY, ['primary' => $menu]);

        $this->get('/')->assertOk()->assertSee('/media/image/', false)->assertDontSee('uploads/navigation/panel.jpg"', false);

        File::delete(ProtectedMedia::root('images/uploads/navigation/panel.jpg'));
    }

    public function test_admin_edits_the_home_hero(): void
    {
        $this->get('/')->assertSee('Your Local Guide to Unforgettable Safaris')->assertSee('Explore Experiences');

        $form = Livewire::test(EditHomePage::class)->assertOk();
        // The headline fields show the current headlines as text.
        $this->assertSame(HomePage::defaults()['hero_phrases'], array_column(array_values($form->get('data.hero_phrases')), 'text'));

        $form
            ->set('data.hero_phrases', [['text' => 'Short one'], ['text' => 'A much longer headline about safaris']])
            ->set('data.hero_subtitle', 'Private safaris and Kilimanjaro climbs')
            ->set('data.hero_cta_label', 'See Our Trips')
            ->set('data.hero_cta2_label', 'Plan My Trip')
            ->set('data.hero_cta2_url', '/contact-us#request')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/')
            ->assertOk()
            ->assertSee('Private safaris and Kilimanjaro climbs')
            ->assertSee('See Our Trips')
            ->assertSee('Plan My Trip')
            ->assertSee(url('/contact-us#request'), false)
            // Longest headline first, so the headline keeps its size while typing.
            ->assertSeeInOrder(['A much longer headline about safaris.', 'Short one.']);

        // A second button needs a link; links must be site paths or http(s).
        Livewire::test(EditHomePage::class)
            ->set('data.hero_cta2_url', '')
            ->call('save')
            ->assertHasFormErrors(['hero_cta2_url' => 'required']);
    }

    public function test_uploaded_video_plays_as_a_signed_file_when_it_cannot_be_converted(): void
    {
        File::ensureDirectoryExists(ProtectedMedia::root('images/uploads/home'));
        File::put(ProtectedMedia::root('images/uploads/home/clip.mp4'), 'mp4');
        SiteSetting::put(HomePage::KEY, ['hero_video' => 'uploads/home/clip.mp4', 'hero_stream' => null]);

        $video = HomePage::hero()['video'];
        $this->assertSame('file', $video['type']);
        $this->get('/')->assertSee('data-src="', false)->assertDontSee('data-stream="', false);

        // Turning the video off shows only the photo.
        SiteSetting::put(HomePage::KEY, ['hero_video' => 'uploads/home/clip.mp4', 'hero_show_video' => false]);
        $this->assertNull(HomePage::hero()['video']);
        $this->get('/')->assertDontSee('data-src="', false);

        File::delete(ProtectedMedia::root('images/uploads/home/clip.mp4'));
    }
}
