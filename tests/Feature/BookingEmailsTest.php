<?php

namespace Tests\Feature;

use App\Filament\Pages\EditSiteSettings;
use App\Mail\TripRequestReceived;
use App\Models\SiteSetting;
use App\Models\TripRequest;
use App\Models\User;
use App\Support\BookingEmails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class BookingEmailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['site.bookings_email' => null, 'site.email' => null]);
    }

    protected function tripRequest(): array
    {
        return [
            'name' => 'Jane Traveller',
            'email' => 'jane@example.com',
            'trip' => 'Machame Route',
            'travel_date' => now()->addMonths(3)->toDateString(),
            'adults' => 2,
        ];
    }

    public function test_recipients_come_from_the_admin_then_env_then_site_email(): void
    {
        $this->assertSame([], BookingEmails::recipients());

        config(['site.email' => 'hello@example.com']);
        $this->assertSame(['hello@example.com'], BookingEmails::recipients());

        config(['site.bookings_email' => 'Bookings@example.com, not-an-email, owner@example.com']);
        $this->assertSame(['bookings@example.com', 'owner@example.com'], BookingEmails::recipients());

        SiteSetting::put(BookingEmails::KEY, ['emails' => ['office@example.com']]);
        $this->assertSame(['office@example.com'], BookingEmails::recipients());

        // Clearing the admin list falls back to .env again.
        SiteSetting::put(BookingEmails::KEY, ['emails' => []]);
        $this->assertSame(['bookings@example.com', 'owner@example.com'], BookingEmails::recipients());
    }

    public function test_a_new_trip_request_is_emailed_and_shows_in_the_admin_bell(): void
    {
        Mail::fake();
        $admin = User::factory()->create();
        SiteSetting::put(BookingEmails::KEY, ['emails' => ['office@example.com', 'owner@example.com']]);

        $this->post('/contact-us', $this->tripRequest())->assertRedirect();

        $request = TripRequest::sole();
        Mail::assertSent(TripRequestReceived::class, fn ($mail) => $mail->hasTo('office@example.com') && $mail->hasTo('owner@example.com') && $mail->tripRequest->is($request));
        $this->assertSame('New trip request from Jane Traveller', $admin->notifications()->sole()->data['title']);
    }

    public function test_the_email_links_to_the_request_in_the_admin(): void
    {
        $request = TripRequest::create($this->tripRequest());

        $this->assertStringContainsString('/admin/trip-requests/'.$request->id, (new TripRequestReceived($request))->render());
    }

    public function test_a_mail_failure_does_not_lose_the_booking(): void
    {
        config(['site.bookings_email' => 'office@example.com']);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));

        $this->post('/contact-us', $this->tripRequest())
            ->assertRedirect()
            ->assertSessionHas('trip_request_sent');

        $this->assertSame(1, TripRequest::count());
    }

    public function test_admin_sets_the_addresses_and_sends_a_test_email(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(EditSiteSettings::class)
            ->fillForm(['booking' => ['emails' => ['Office@Example.com', 'owner@example.com']]])
            ->call('save')
            ->assertHasNoFormErrors()
            ->call('sendTestEmail')
            ->assertNotified('Test email sent');

        $this->assertSame(['office@example.com', 'owner@example.com'], BookingEmails::recipients());

        $sent = app('mailer')->getSymfonyTransport()->messages(); // MAIL_MAILER=array in tests
        $this->assertCount(1, $sent);
        $this->assertSame(['office@example.com', 'owner@example.com'], array_map(fn ($a) => $a->getAddress(), $sent->first()->getOriginalMessage()->getTo()));
    }

    public function test_invalid_addresses_are_rejected_in_the_admin(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(EditSiteSettings::class)
            ->fillForm(['booking' => ['emails' => ['not an email']]])
            ->call('save')
            ->assertHasFormErrors();

        $this->assertSame([], BookingEmails::saved());
    }
}
