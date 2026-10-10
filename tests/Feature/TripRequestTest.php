<?php

namespace Tests\Feature;

use App\Mail\TripRequestReceived;
use App\Models\TripRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TripRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Traveller',
            'email' => 'jane@example.com',
            'phone' => '+44 7700 900000',
            'country' => 'United Kingdom',
            'trip' => 'Machame Route',
            'travel_date' => now()->addMonths(3)->toDateString(),
            'duration_days' => 8,
            'adults' => 2,
            'children' => 1,
            'interests' => ['kilimanjaro', 'zanzibar'],
            'budget' => 'mid-range',
            'message' => 'Climb then beach, please.',
        ], $overrides);
    }

    public function test_a_valid_request_is_saved_and_the_visitor_sees_a_thank_you(): void
    {
        $this->from('/contact-us')->post('/contact-us', $this->validData())
            ->assertRedirect(route('contact').'#request');

        $request = TripRequest::sole();
        $this->assertSame('Jane Traveller', $request->name);
        $this->assertSame(['kilimanjaro', 'zanzibar'], $request->interests);
        $this->assertSame(1, $request->children);

        $this->get('/contact-us')->assertSee('Thank you, Jane Traveller!');
    }

    public function test_only_name_email_and_adults_are_required(): void
    {
        $this->post('/contact-us', ['name' => 'Sam', 'email' => 'sam@example.com', 'adults' => 1])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, TripRequest::sole()->children);
    }

    public function test_invalid_input_returns_to_the_form_with_errors(): void
    {
        $this->from('/contact-us')->post('/contact-us', $this->validData([
            'name' => '',
            'email' => 'nope',
            'travel_date' => now()->subDay()->toDateString(),
            'interests' => ['space-travel'],
            'budget' => 'unlimited',
        ]))
            ->assertRedirect(route('contact').'#request')
            ->assertSessionHasErrorsIn('tripRequest', ['name', 'email', 'travel_date', 'interests.0', 'budget']);

        $this->assertSame(0, TripRequest::count());
    }

    public function test_office_is_emailed_when_site_email_is_set(): void
    {
        Mail::fake();
        config(['site.email' => 'office@example.com']);

        $this->post('/contact-us', $this->validData());

        Mail::assertSent(TripRequestReceived::class, fn ($mail) => $mail->hasTo('office@example.com') && $mail->hasReplyTo('jane@example.com'));
    }

    public function test_no_email_is_attempted_without_site_email(): void
    {
        Mail::fake();
        config(['site.email' => null]);

        $this->post('/contact-us', $this->validData());

        Mail::assertNothingSent();
        $this->assertSame(1, TripRequest::count());
    }

    public function test_honeypot_submissions_are_not_stored(): void
    {
        $this->post('/contact-us', $this->validData(['website' => 'http://spam.example']))
            ->assertRedirect(route('contact').'#request');

        $this->assertSame(0, TripRequest::count());
    }

    public function test_notification_email_renders(): void
    {
        $request = TripRequest::create($this->validData());

        $text = (new TripRequestReceived($request))->render();

        $this->assertStringContainsString('Jane Traveller', $text);
        $this->assertStringContainsString('Kilimanjaro, Zanzibar', $text);
        $this->assertStringContainsString('2 adult(s), 1 child(ren)', $text);
    }
}
