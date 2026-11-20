<?php

namespace Tests\Feature;

use App\Filament\Pages\EditLegalPages;
use App\Models\User;
use App\Support\LegalPages;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
        config(['site.email' => 'hello@shanyangi.example', 'site.phone' => '+255 787 156 775']);
    }

    public function test_each_legal_page_loads_with_its_sections(): void
    {
        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('On this page')
            ->assertSee('id="information-you-give-us"', false)
            ->assertSee('hello@shanyangi.example')
            ->assertSee(config('analytics.retention_days').' days')
            ->assertDontSee('{email}');

        $this->get(route('legal.terms'))
            ->assertOk()
            ->assertSee('Terms &amp; Conditions', false)
            ->assertSee('id="2-deposit-and-balance-payment"', false)
            ->assertSee('id="23-acceptance-of-terms"', false)
            ->assertSee('40% deposit', false)
            ->assertSee('$44 per person')
            ->assertSee('Your Journey, Our Commitment.')
            ->assertSee('Last updated', false)
            ->assertSee(route('legal.refund'), false);

        $this->get(route('legal.refund'))
            ->assertOk()
            ->assertSee('Refund Policy')
            ->assertSee('<table>', false)
            ->assertSee('Amount we may retain')
            ->assertSee('Up to 75% of the total safari cost');
    }

    public function test_old_terms_link_redirects_and_footer_links_to_all_pages(): void
    {
        $this->get('/terms')->assertRedirect('/terms-and-conditions');

        $this->get('/')
            ->assertSee(route('legal.privacy'), false)
            ->assertSee(route('legal.terms'), false)
            ->assertSee(route('legal.refund'), false);
    }

    public function test_admin_edits_a_legal_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(EditLegalPages::getUrl())->assertOk()->assertSee('Legal pages');

        $edited = LegalPages::defaultBody('refund')."\n\n## Group bookings\n\nGroups of 10 or more: contact {email}.";

        Livewire::test(EditLegalPages::class)
            ->fillForm(['refund' => ['body' => $edited]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(now()->toDateString(), LegalPages::updatedOn('refund')->toDateString());
        $this->assertSame(LegalPages::DEFAULT_UPDATED, LegalPages::updatedOn('privacy')->toDateString()); // untouched pages keep their date

        $this->get(route('legal.refund'))
            ->assertSee('Group bookings')
            ->assertSeeText('Groups of 10 or more: contact hello@shanyangi.example.')
            ->assertSee('href="mailto:hello@shanyangi.example"', false); // emails become clickable

        Livewire::test(EditLegalPages::class)->callAction('restore');
        $this->get(route('legal.refund'))->assertDontSee('Group bookings');
    }

    public function test_html_typed_into_the_editor_is_not_rendered(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(EditLegalPages::class)
            ->fillForm(['privacy' => ['body' => "## Hello\n\n<script>alert(1)</script> [bad](javascript:alert(1))"]])
            ->call('save');

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('href="javascript:', false);
    }
}
