<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaUpload;
use App\Models\SiteSetting;
use App\Support\Navigation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Edit the header menu: items, their dropdown links, and the text and photo
 * shown in each dropdown panel. The footer "Explore" links follow it too.
 *
 * @property-read Schema $form
 */
class EditNavigation extends Page
{
    protected static ?string $navigationLabel = 'Navigation menu';

    protected static ?string $title = 'Navigation menu';

    protected static ?string $slug = 'navigation-menu';

    protected static string|UnitEnum|null $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(['items' => Navigation::toForm(Navigation::primary())]);
    }

    /** Link field shared by menu items and dropdown links. */
    protected static function pathInput(string $placeholder): TextInput
    {
        return TextInput::make('path')
            ->label('Link')
            ->required()
            ->maxLength(255)
            ->regex(Navigation::PATH_PATTERN)
            ->validationMessages(['regex' => 'Start with "/" for a page on this site (e.g. /safaris?type=family), "#" for a section, or https:// for another website.'])
            ->placeholder($placeholder);
    }

    /** Photo field: an upload, or the current online photo kept in "image_url". */
    protected static function imageInput(string $label): array
    {
        return [
            MediaUpload::make('image', 'navigation')
                ->label($label)
                ->helperText(fn (Get $get) => filled($get('image_url'))
                    ? 'Currently using an online photo. Upload one to replace it; leave empty to keep it.'
                    : 'A landscape photo works best.')
                ->columnSpanFull(),
            Hidden::make('image_url'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Repeater::make('items')
                    ->label('Menu items')
                    ->helperText('The menu at the top of every page, in this order. Items with dropdown links open a panel with a photo and text on desktop, and an accordion on phones.')
                    ->itemLabel(fn (array $state) => ($state['label'] ?? null) ?: 'New menu item')
                    ->addActionLabel('Add menu item')
                    ->minItems(1)
                    ->maxItems(Navigation::MAX_ITEMS)
                    ->collapsible()
                    ->collapsed()
                    ->reorderableWithButtons()
                    ->defaultItems(0)
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')->label('Menu label')->required()->maxLength(40)->placeholder('Safari'),
                        static::pathInput('/safaris'),

                        Repeater::make('children')
                            ->label('Dropdown links')
                            ->helperText('Leave empty for a plain link without a dropdown. Give a link its own panel text to change the panel while visitors point at it.')
                            ->itemLabel(fn (array $state) => ($state['label'] ?? null) ?: 'New link')
                            ->addActionLabel('Add dropdown link')
                            ->maxItems(Navigation::MAX_CHILDREN)
                            ->collapsible()
                            ->collapsed()
                            ->reorderableWithButtons()
                            ->defaultItems(0)
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('label')->required()->maxLength(40)->placeholder('Family'),
                                static::pathInput('/safaris?type=family'),
                                Fieldset::make('Panel text for this link (optional)')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('title')->maxLength(80)->placeholder('Family Safaris'),
                                        TextInput::make('cta')->label('Button text')->maxLength(40)->placeholder('View Family Safaris'),
                                        Textarea::make('text')->rows(2)->maxLength(240)->columnSpanFull(),
                                        ...static::imageInput('Panel photo'),
                                    ]),
                            ]),

                        Section::make('Dropdown panel')
                            ->description('Shown when the dropdown opens, before visitors point at a link.')
                            ->statePath('mega')
                            ->compact()
                            ->collapsible()
                            ->collapsed()
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => filled($get('children')))
                            ->schema([
                                TextInput::make('title')->maxLength(80)->placeholder('Tanzania Safari Adventures'),
                                TextInput::make('cta')->label('Button text')->maxLength(40)->placeholder('Explore Safaris'),
                                Textarea::make('text')->rows(2)->maxLength(240)->columnSpanFull(),
                                ...static::imageInput('Panel photo'),
                            ]),
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
                    Action::make('save')->label('Save menu')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View website')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(url('/'), shouldOpenInNewTab: true),
            Action::make('restore')
                ->label('Restore original menu')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Replaces your menu with the original one. Uploaded photos stay in storage. This cannot be undone.')
                ->action(function () {
                    SiteSetting::put(Navigation::KEY, []);
                    $this->fillForm();
                    Notification::make()->title('Original menu restored')->success()->send();
                }),
        ];
    }

    public function save(): void
    {
        $items = Navigation::fromForm($this->form->getState()['items'] ?? []);

        SiteSetting::put(Navigation::KEY, ['primary' => $items]);

        Notification::make()->title('Menu saved')->body('The website shows the new menu straight away.')->success()->send();
    }
}
