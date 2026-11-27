<?php

namespace Tests\Feature;

use App\Filament\Pages\EditSiteSettings;
use App\Filament\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Resources\Quotes\Pages\EditQuote;
use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Mail\InstantQuoteCreated;
use App\Mail\QuoteMail;
use App\Models\Package;
use App\Models\Quote;
use App\Models\SiteSetting;
use App\Models\TripRequest;
use App\Models\User;
use App\Support\BankDetails;
use App\Support\QuotePdf;
use Database\Seeders\ContentSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
    }

    protected function pricedPackage(): Package
    {
        return Package::query()->published()->where('price', '>', 0)->whereNull('price_tiers')->firstOrFail();
    }

    protected function visitorForm(array $overrides = []): array
    {
        return [
            'name' => 'Jane Traveller',
            'email' => 'jane@example.com',
            'phone' => '+44 7700 900123',
            'country' => 'United Kingdom',
            'travel_date' => now()->addMonths(2)->toDateString(),
            'adults' => 2,
            'children' => 1,
            ...$overrides,
        ];
    }

    public function test_reference_and_totals_are_calculated(): void
    {
        $quote = Quote::create([
            'customer_name' => 'Jane',
            'customer_email' => 'jane@example.com',
            'adults' => 2,
            'children' => 0,
            'items' => [
                ['description' => 'Safari', 'quantity' => 2, 'unit_price' => 1500],
                ['description' => 'Park fees', 'quantity' => 2, 'unit_price' => 120.5],
            ],
            'discount' => 100,
        ]);

        $this->assertSame(sprintf('QT-%s-%06d', now()->format('Y'), $quote->id), $quote->reference);
        $this->assertSame(3241.0, $quote->subtotal);
        $this->assertSame(3141.0, $quote->total);
        $this->assertSame('$3,141.00', Quote::money($quote->total));
    }

    public function test_bank_details_are_managed_in_site_settings_and_printed_on_quotes(): void
    {
        $this->actingAs(User::factory()->create());

        Repeater::fake();
        Livewire::test(EditSiteSettings::class)
            ->fillForm(['bank' => [
                'accounts' => [
                    ['bank_name' => 'CRDB Bank PLC', 'account_name' => 'Shanyangi Adventures Ltd', 'account_number' => '0150123456789', 'branch' => 'Arusha', 'swift' => 'CORUTZTZ', 'currency' => 'USD'],
                    ['bank_name' => 'NMB Bank', 'account_name' => 'Shanyangi Adventures Ltd', 'account_number' => '40110012345', 'branch' => '', 'swift' => '', 'currency' => 'TZS'],
                ],
                'note' => 'Bank charges are paid by the sender.',
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['CRDB Bank PLC', 'NMB Bank'], array_column(BankDetails::accounts(), 'bank_name'));

        $quote = Quote::create(['customer_name' => 'Jane', 'customer_email' => 'jane@example.com', 'adults' => 2, 'children' => 0, 'items' => []]);
        $html = view('pdf.quote', ['quote' => $quote])->render();
        $this->assertStringContainsString('Payment details', $html);
        $this->assertStringContainsString('0150123456789', $html);
        $this->assertStringContainsString('CORUTZTZ', $html);
        $this->assertStringContainsString('40110012345', $html);
        $this->assertStringContainsString('Bank charges are paid by the sender.', $html);
        $this->assertStringContainsString('Payment reference: <strong>'.$quote->reference, $html);
        $this->assertStringStartsWith('%PDF', QuotePdf::output($quote));

        // Switched off for one quote.
        $quote->update(['show_bank_details' => false]);
        $this->assertStringNotContainsString('Payment details', view('pdf.quote', ['quote' => $quote->fresh()])->render());
    }

    public function test_no_payment_box_until_an_account_is_complete(): void
    {
        SiteSetting::put(BankDetails::KEY, ['accounts' => [['bank_name' => 'CRDB Bank PLC', 'account_number' => '']]]);
        $quote = Quote::create(['customer_name' => 'Jane', 'customer_email' => 'jane@example.com', 'adults' => 2, 'children' => 0, 'items' => []]);

        $this->assertFalse(BankDetails::isConfigured());
        $this->assertTrue($quote->fresh()->show_bank_details); // on by default
        $this->assertStringNotContainsString('Payment details', view('pdf.quote', ['quote' => $quote])->render());
    }

    public function test_quote_form_has_the_bank_details_switch(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(CreateQuote::getUrl())->assertOk()->assertSee('Show bank details on the PDF')->assertSee('No bank accounts yet');

        $quote = Quote::create(['customer_name' => 'Jane', 'customer_email' => 'jane@example.com', 'adults' => 2, 'children' => 0, 'items' => [['description' => 'Safari', 'quantity' => 1, 'unit_price' => 100]]]);
        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->fillForm(['show_bank_details' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($quote->fresh()->show_bank_details);
    }

    public function test_pdf_contains_the_quote_details(): void
    {
        $package = $this->pricedPackage();
        $quote = Quote::create([
            'package_id' => $package->id,
            'customer_name' => 'Jane Traveller',
            'customer_email' => 'jane@example.com',
            'adults' => 2,
            'children' => 1,
            'travel_start' => now()->addMonth(),
            'items' => QuotePdf::packageItems($package, 2, 1),
        ]);

        $pdf = QuotePdf::output($quote);

        $this->assertStringStartsWith('%PDF', $pdf);
        $html = view('pdf.quote', ['quote' => $quote])->render();
        $this->assertStringContainsString($quote->reference, $html);
        $this->assertStringContainsString('Jane Traveller', $html);
        $this->assertStringContainsString(e($package->title), $html);
        $this->assertStringContainsString(Quote::money($quote->total), $html);
    }

    public function test_package_page_offers_an_instant_quote_only_when_priced(): void
    {
        $package = $this->pricedPackage();
        $this->get(route('safaris.show', $package->slug))
            ->assertOk()
            ->assertSee('Get an Instant Quote')
            ->assertSee(route('quotes.store', $package->slug), false);

        $package->update(['price' => null]);
        $this->get(route('safaris.show', $package->slug))
            ->assertOk()
            ->assertDontSee('Get an Instant Quote');
        $this->post(route('quotes.store', $package->slug), $this->visitorForm())->assertNotFound();
    }

    public function test_visitor_gets_a_quote_and_a_signed_download_link(): void
    {
        Mail::fake();
        config(['site.bookings_email' => 'office@example.com']);
        $admin = User::factory()->create();
        $package = $this->pricedPackage();

        $response = $this->post(route('quotes.store', $package->slug), $this->visitorForm());

        $quote = Quote::sole();
        $response->assertRedirect(route('safaris.show', $package->slug).'#instant-quote')
            ->assertSessionHas('instant_quote.reference', $quote->reference);

        $this->assertSame('website', $quote->source);
        $this->assertSame($package->id, $quote->package_id);
        $this->assertSame(2, $quote->adults);
        $this->assertSame(1, $quote->children);
        $this->assertCount(2, $quote->items); // adults + children lines
        $this->assertEquals($package->price * 2 + $package->price * config('quotes.child_rate_percent') / 100, $quote->total);
        $this->assertSame(config('quotes.instant_note'), $quote->notes);

        Mail::assertSent(InstantQuoteCreated::class, fn ($mail) => $mail->hasTo('office@example.com') && $mail->quote->is($quote));
        $this->assertSame(1, $admin->notifications()->count());

        // The success message links to the PDF; the link only works while signed.
        $url = session('instant_quote.url');
        $this->get(route('safaris.show', $package->slug))
            ->assertSee('Your quote is ready')
            ->assertSee($quote->reference)
            ->assertSee(e($url), false);

        $this->get($url)->assertOk()->assertDownload($quote->pdfFilename());
        $this->get(route('quotes.pdf', $quote))->assertForbidden();
        $this->get(URL::temporarySignedRoute('quotes.pdf', now()->subMinute(), ['quote' => $quote->id]))->assertForbidden();
    }

    public function test_visitor_quote_validation_and_honeypot(): void
    {
        $package = $this->pricedPackage();
        $page = route('safaris.show', $package->slug).'#instant-quote';

        $this->post(route('quotes.store', $package->slug), $this->visitorForm(['email' => 'nope', 'travel_date' => now()->subDay()->toDateString(), 'adults' => 0]))
            ->assertRedirect($page)
            ->assertSessionHasErrorsIn('quote', ['email', 'travel_date', 'adults']);

        $this->post(route('quotes.store', $package->slug), $this->visitorForm(['website' => 'http://spam.example']))
            ->assertRedirect($page);

        $this->assertSame(0, Quote::count());
    }

    public function test_quote_admin_screens_load(): void
    {
        $this->actingAs(User::factory()->create());
        $quote = Quote::create(['customer_name' => 'Jane', 'customer_email' => 'jane@example.com', 'adults' => 2, 'children' => 0, 'items' => []]);

        $this->get(ListQuotes::getUrl())->assertOk()->assertSee($quote->reference);
        $this->get(CreateQuote::getUrl())->assertOk();
        $this->get(EditQuote::getUrl(['record' => $quote]))->assertOk()->assertSee($quote->reference);
    }

    public function test_admin_creates_a_quote_from_an_inquiry(): void
    {
        $this->actingAs(User::factory()->create());
        $package = $this->pricedPackage();
        $inquiry = TripRequest::create([
            'name' => 'Sam Explorer', 'email' => 'sam@example.com', 'country' => 'Kenya', 'trip' => $package->title,
            'travel_date' => now()->addMonths(3)->toDateString(), 'duration_days' => 5, 'adults' => 3, 'children' => 0,
        ]);

        $this->get(route('filament.admin.resources.trip-requests.view', $inquiry))
            ->assertSee(CreateQuote::getUrl(['trip_request' => $inquiry->id]), false);

        Repeater::fake();
        Livewire::withQueryParams(['trip_request' => $inquiry->id])
            ->test(CreateQuote::class)
            ->assertSchemaStateSet([
                'customer_name' => 'Sam Explorer',
                'customer_email' => 'sam@example.com',
                'customer_country' => 'Kenya',
                'adults' => 3,
                'package_id' => $package->id,
                'travel_end' => $inquiry->travel_date->copy()->addDays(4)->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $quote = Quote::sole();
        $this->assertSame($inquiry->id, $quote->trip_request_id);
        $this->assertSame('admin', $quote->source);
        $this->assertEquals($package->price * 3, $quote->total);
    }

    public function test_admin_downloads_and_emails_a_quote(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());
        $quote = Quote::create([
            'customer_name' => 'Jane', 'customer_email' => 'jane@example.com', 'adults' => 2, 'children' => 0,
            'items' => [['description' => 'Safari', 'quantity' => 2, 'unit_price' => 1000]],
        ]);

        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->callAction('pdf')
            ->assertFileDownloaded($quote->pdfFilename());

        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->callAction('email');

        Mail::assertSent(QuoteMail::class, fn ($mail) => $mail->hasTo('jane@example.com'));
        $this->assertSame('sent', $quote->fresh()->status);
    }
}
