<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\BankDetails;
use App\Support\BookingEmails;
use App\Support\ContactDetails;
use App\Support\SocialLinks;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

/**
 * Contact details, booking email addresses, social media links (footer, contact page,
 * mobile menu) and the bank accounts printed on quotation PDFs.
 *
 * @property-read Schema $form
 */
class EditSiteSettings extends Page
{
    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static ?string $slug = 'site-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $bank = SiteSetting::raw(BankDetails::KEY) ?? [];

        $this->form->fill([
            'contact' => ContactDetails::current(),
            'social' => SocialLinks::urls(),
            'booking' => ['emails' => BookingEmails::saved()],
            'bank' => [
                'accounts' => array_values((array) ($bank['accounts'] ?? [])),
                'note' => $bank['note'] ?? 'Please use the quotation reference as the payment reference. International transfer charges are paid by the sender.',
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $fields = [];
        foreach (SocialLinks::NETWORKS as $key => $network) {
            $fields[] = TextInput::make($key)
                ->label($network['label'])
                ->url()
                ->maxLength(255)
                ->placeholder('https://www.'.$key.'.com/your-page');
        }

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Contact details')
                    ->description('Shown on the Contact Us page, in the footer, on the mobile call / WhatsApp buttons and on quote PDFs. Leave a field empty to hide it.')
                    ->icon(Heroicon::OutlinedPhone)
                    ->statePath('contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('WhatsApp number')
                            ->tel()
                            ->maxLength(30)
                            ->regex('/^\+?[\d\s()-]{7,}$/')
                            ->placeholder('+255 787 156 775')
                            ->helperText('With the country code. Used for every WhatsApp button.'),
                        TextInput::make('phone')
                            ->label('Phone number')
                            ->tel()
                            ->maxLength(30)
                            ->regex('/^\+?[\d\s()-]{7,}$/')
                            ->placeholder('+255 787 156 775')
                            ->helperText('For the "Call us" buttons.'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('info@shanyangiadventures.co.tz'),
                        TextInput::make('address')
                            ->label('Office address')
                            ->maxLength(255)
                            ->placeholder('e.g. Arusha, Tanzania'),
                    ]),
                Section::make('Booking emails')
                    ->description('Every new trip request and instant quote is emailed to these addresses, as well as appearing in the admin. Press Enter after each address.')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->statePath('booking')
                    ->schema([
                        TagsInput::make('emails')
                            ->hiddenLabel()
                            ->placeholder('e.g. info@shanyangiadventures.co.tz')
                            ->nestedRecursiveRules(['email:rfc'])
                            ->splitKeys([',', ' '])
                            ->helperText(fn () => BookingEmails::saved() ? null : 'Not set yet: emails currently go to '.(implode(', ', BookingEmails::recipients()) ?: 'nobody').' (from .env).'),
                        Actions::make([
                            Action::make('testEmail')
                                ->label('Send test email')
                                ->icon(Heroicon::OutlinedPaperAirplane)
                                ->color('gray')
                                ->action(fn () => $this->sendTestEmail()),
                        ]),
                    ]),
                Section::make('Social media')
                    ->description('Your official accounts. Icons appear in the website footer, the contact page and the mobile menu. Leave a field empty to hide that network.')
                    ->statePath('social')
                    ->columns(2)
                    ->schema($fields),
                Section::make('Bank details')
                    ->description('Printed in the "Payment details" box of every quotation PDF (you can switch it off per quote). Add one account per currency if you have several.')
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->statePath('bank')
                    ->schema([
                        Repeater::make('accounts')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('bank_name')->label('Bank name')->required()->maxLength(120)->placeholder('e.g. CRDB Bank PLC'),
                                TextInput::make('account_name')->label('Account name')->maxLength(120)->placeholder('e.g. Shanyangi Adventures Ltd'),
                                TextInput::make('account_number')->label('Account number')->required()->maxLength(60),
                                TextInput::make('branch')->maxLength(120)->placeholder('e.g. Arusha Branch'),
                                TextInput::make('swift')->label('SWIFT / BIC code')->maxLength(20)->placeholder('For international transfers'),
                                TextInput::make('currency')->maxLength(10)->placeholder('USD or TZS'),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state) => trim(($state['bank_name'] ?? '').(filled($state['currency'] ?? null) ? ' · '.$state['currency'] : '')) ?: 'Bank account')
                            ->addActionLabel('Add bank account')
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->collapsible(),
                        Textarea::make('note')
                            ->label('Payment instructions')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Shown under the accounts on the quote, e.g. deposit amount or who pays transfer charges.'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $social = array_map(fn ($url) => $url ?: null, $state['social'] ?? []);

        SiteSetting::put(SocialLinks::KEY, $social);
        SiteSetting::put(ContactDetails::KEY, array_map(fn ($value) => filled($value) ? trim($value) : null, $state['contact'] ?? []));
        SiteSetting::put(BookingEmails::KEY, [
            'emails' => array_values(array_unique(array_map(fn ($email) => strtolower(trim($email)), $state['booking']['emails'] ?? []))),
        ]);
        SiteSetting::put(BankDetails::KEY, [
            'accounts' => array_values($state['bank']['accounts'] ?? []),
            'note' => $state['bank']['note'] ?? null,
        ]);

        Notification::make()->title('Site settings saved')->success()->send();
    }

    /** Checks the mail settings by sending a short email to the saved booking addresses. */
    public function sendTestEmail(): void
    {
        if (! $to = BookingEmails::recipients()) {
            Notification::make()->title('Add a booking email first')->body('Type an address, press Enter, then Save changes.')->warning()->send();

            return;
        }

        try {
            Mail::raw(
                "This is a test from the ".config('app.name')." website.

New bookings (trip requests and instant quotes) will be emailed to this address.",
                fn ($message) => $message->to($to)->subject('Test: booking emails are working'),
            );
        } catch (Throwable $e) {
            Notification::make()
                ->title('Email could not be sent')
                ->body('Check the MAIL_ settings in .env. Error: '.str($e->getMessage())->limit(200))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()->title('Test email sent')->body('Sent to '.implode(', ', $to).'. Check the inbox (and the spam folder).')->success()->send();
    }
}
