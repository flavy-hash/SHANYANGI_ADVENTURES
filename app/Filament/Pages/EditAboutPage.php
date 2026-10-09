<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaUpload;
use App\Models\SiteSetting;
use App\Support\AboutPage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
use UnitEnum;

/**
 * Edits the About Us page (/about-us). Stored as the "about" site setting;
 * empty fields fall back to the defaults in App\Support\AboutPage.
 *
 * @property-read Schema $form
 */
class EditAboutPage extends Page
{
    protected static ?string $navigationLabel = 'About Us';

    protected static ?string $title = 'About Us page';

    protected static ?string $slug = 'about-page';

    protected static string|UnitEnum|null $navigationGroup = 'Pages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(AboutPage::content());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Page banner')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_title')->label('Title')->required()->maxLength(120),
                        TextInput::make('hero_subtitle')->label('Subtitle')->maxLength(255),
                        MediaUpload::make('hero_image', 'about')
                            ->label('Banner photo')
                            ->helperText('Leave empty to use the default photo.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Our story')
                    ->columns(2)
                    ->schema([
                        TextInput::make('story_title')->label('Heading')->required()->maxLength(120)->columnSpanFull(),
                        Textarea::make('story')
                            ->label('Story text')
                            ->required()
                            ->rows(10)
                            ->helperText('Leave a blank line between paragraphs.')
                            ->columnSpanFull(),
                        MediaUpload::make('story_image', 'about')
                            ->label('Story photo')
                            ->helperText('Leave empty to use the default photo.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Our team')
                    ->description('Shown as cards in the "Our Team" section (#team). Describe roles, or add real team members.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('team_title')->label('Heading')->required()->maxLength(120),
                        TextInput::make('team_subtitle')->label('Intro line')->maxLength(255),
                        Repeater::make('team')
                            ->label('Cards')
                            ->schema([
                                Select::make('icon')
                                    ->options(collect(AboutPage::icons())->keys()->mapWithKeys(fn ($key) => [$key => ucfirst($key)])->all())
                                    ->default('users')
                                    ->required(),
                                TextInput::make('title')->required()->maxLength(80),
                                Textarea::make('text')->rows(2)->maxLength(255)->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state) => $state['title'] ?? null)
                            ->collapsible()
                            ->reorderableWithButtons()
                            ->maxItems(8)
                            ->addActionLabel('Add card')
                            ->columnSpanFull(),
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
                    Action::make('save')
                        ->label('Save changes')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                    Action::make('view')
                        ->label('View page')
                        ->color('gray')
                        ->url(route('about'), shouldOpenInNewTab: true),
                ]),
            ]);
    }

    public function save(): void
    {
        SiteSetting::put(AboutPage::KEY, $this->form->getState());

        Notification::make()
            ->title('About Us page saved')
            ->success()
            ->send();
    }
}
