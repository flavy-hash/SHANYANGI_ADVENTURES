<?php

namespace Tests\Feature;

use App\Filament\Pages\EditSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\ContactDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_and_footer_show_email_phone_and_whatsapp_number(): void
    {
        config(['site.email' => 'info@example.com', 'site.phone' => '+255787156775', 'site.whatsapp' => '255787156775']);

        $html = $this->get('/contact-us')->assertOk()->getContent();

        // Contact card and footer each show the email and both numbers.
        $this->assertSame(2, substr_count($html, 'href="mailto:info@example.com"'));
        $this->assertSame(3, substr_count($html, 'href="tel:+255787156775"')); // + the mobile "Call" button
        $this->assertStringContainsString('<strong>WhatsApp</strong><small>+255 787 156 775</small>', $html);
        $this->assertStringContainsString('WhatsApp: +255 787 156 775', $html);

        // Every page has them in the footer.
        $this->get('/safaris')->assertSee('info@example.com')->assertSee('WhatsApp: +255 787 156 775');
    }

    public function test_numbers_are_shown_in_readable_form(): void
    {
        $this->assertSame('+255 787 156 775', ContactDetails::display('255787156775'));
        $this->assertSame('+255 787 156 775', ContactDetails::display('+255 787-156-775'));
        $this->assertSame('+44 20 7946 0000', ContactDetails::display('+44 20 7946 0000'));
        $this->assertNull(ContactDetails::display(''));
    }

    public function test_admin_values_replace_the_env_values_across_the_site(): void
    {
        SiteSetting::put(ContactDetails::KEY, ['email' => 'bookings@example.com', 'phone' => '+255 700 000 001', 'whatsapp' => '255700000002', 'address' => null]);
        ContactDetails::apply(); // done on every boot

        $this->assertSame('bookings@example.com', config('site.email'));
        $this->assertNull(config('site.address'));

        $this->get('/contact-us')
            ->assertSee('bookings@example.com')
            ->assertSee('+255 700 000 001')
            ->assertSee('https://wa.me/255700000002', false)
            ->assertDontSee('<strong>Office</strong>', false); // empty field = hidden
    }

    public function test_admin_edits_contact_details_in_site_settings(): void
    {
        $this->actingAs(User::factory()->create());
        config(['site.email' => 'info@example.com']);

        Livewire::test(EditSiteSettings::class)
            ->assertSchemaStateSet(['contact.email' => 'info@example.com']) // starts from the current values
            ->fillForm(['contact' => ['email' => 'hello@example.com', 'phone' => '+255 787 156 775', 'whatsapp' => '+255 787 156 775', 'address' => 'Arusha, Tanzania']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals(
            ['email' => 'hello@example.com', 'phone' => '+255 787 156 775', 'whatsapp' => '+255 787 156 775', 'address' => 'Arusha, Tanzania'],
            SiteSetting::raw(ContactDetails::KEY),
        );

        Livewire::test(EditSiteSettings::class)
            ->fillForm(['contact' => ['email' => 'not an email', 'phone' => 'call me']])
            ->call('save')
            ->assertHasFormErrors(['contact.email', 'contact.phone']);
    }
}
