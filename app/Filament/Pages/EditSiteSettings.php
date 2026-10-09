<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\SocialLinks;
use BackedEnum;
use Filament\Actions\Action;
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
use UnitEnum;

/**
 * Official social media links shown in the footer, contact page and mobile menu.
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
        $this->form->fill(['social' => SocialLinks::urls()]);
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
                Section::make('Social media')
                    ->description('Your official accounts. Icons appear in the website footer, the contact page and the mobile menu. Leave a field empty to hide that network.')
                    ->statePath('social')
                    ->columns(2)
                    ->schema($fields),
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
        $social = array_map(fn ($url) => $url ?: null, $this->form->getState()['social'] ?? []);

        SiteSetting::put(SocialLinks::KEY, $social);

        Notification::make()->title('Site settings saved')->success()->send();
    }
}
