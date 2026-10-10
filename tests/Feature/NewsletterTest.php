<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_is_on_the_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('New adventures, first')
            ->assertSee('action="'.route('newsletter.subscribe').'"', false);
    }

    public function test_subscribing_stores_the_email(): void
    {
        $this->postJson('/newsletter', ['email' => ' Traveler@Example.com '])
            ->assertOk()
            ->assertJson(['message' => "Thank you! You're on the list."]);

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'traveler@example.com']);
    }

    public function test_subscribing_twice_gives_the_same_reply_and_one_row(): void
    {
        $this->postJson('/newsletter', ['email' => 'traveler@example.com'])->assertOk();
        $this->postJson('/newsletter', ['email' => 'TRAVELER@example.com'])
            ->assertOk()
            ->assertJson(['message' => "Thank you! You're on the list."]);

        $this->assertSame(1, NewsletterSubscriber::count());
    }

    public function test_resubscribing_clears_unsubscribe(): void
    {
        NewsletterSubscriber::create(['email' => 'traveler@example.com', 'unsubscribed_at' => now()]);

        $this->postJson('/newsletter', ['email' => 'traveler@example.com'])->assertOk();

        $this->assertNull(NewsletterSubscriber::first()->unsubscribed_at);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->postJson('/newsletter', ['email' => 'not-an-email'])
            ->assertStatus(422)
            ->assertJson(['message' => 'Please enter a valid email address.']);

        $this->postJson('/newsletter', [])->assertStatus(422);
        $this->assertSame(0, NewsletterSubscriber::count());
    }

    public function test_honeypot_submissions_are_not_stored(): void
    {
        $this->postJson('/newsletter', ['email' => 'bot@example.com', 'website' => 'http://spam.example'])
            ->assertOk();

        $this->assertSame(0, NewsletterSubscriber::count());
    }

    public function test_without_javascript_it_redirects_back_to_the_form_with_a_message(): void
    {
        $this->from('/')->post('/newsletter', ['email' => 'traveler@example.com'])
            ->assertRedirect(url('/').'#newsletter')
            ->assertSessionHas('newsletter_success');
    }

    public function test_requests_are_rate_limited(): void
    {
        foreach (range(1, 5) as $i) {
            $this->postJson('/newsletter', ['email' => "t{$i}@example.com"])->assertOk();
        }

        $this->postJson('/newsletter', ['email' => 't6@example.com'])->assertStatus(429);
    }
}
