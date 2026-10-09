<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Package')
                    ->persistTabInQueryString()
                    ->tabs([
                        self::cardTab(),
                        self::overviewTab(),
                        self::itineraryTab(),
                        self::inclusionsTab(),
                    ]),
            ]);
    }

    protected static function cardTab(): Tab
    {
        return Tab::make('Card & banner')
            ->icon('heroicon-o-photo')
            ->columns(2)
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Web address')
                    ->prefix('/safaris/')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText('Changing this breaks old links to the package.'),
                TextInput::make('category')
                    ->required()
                    ->maxLength(50)
                    ->datalist(['Safari', 'Kilimanjaro', 'Zanzibar', 'Honeymoon', 'Family', 'Luxury', 'Migration'])
                    ->helperText('Shown as the badge on the photo; also groups reviews.'),
                TextInput::make('tagline')
                    ->maxLength(255)
                    ->placeholder('e.g. The classic northern circuit'),
                Textarea::make('summary')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Short description on the package card and in the page banner.'),
                MediaUpload::make('image', 'packages')
                    ->label('Main photo')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Price from (per person)')
                    ->numeric()
                    ->minValue(0)
                    ->prefix(config('packages.currency', '$'))
                    ->helperText('Leave empty to show "Price on request".'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first. You can also drag rows in the list.'),
                Repeater::make('chips')
                    ->label('Card tags')
                    ->helperText('Short facts shown as pills on the card. The first one should be the duration (e.g. "5 Days").')
                    ->schema([
                        TextInput::make('label')->required()->maxLength(40),
                        Toggle::make('highlight')->label('Highlight (orange)')->inline(false),
                    ])
                    ->columns(2)
                    ->maxItems(4)
                    ->defaultItems(0)
                    ->addActionLabel('Add tag')
                    ->reorderableWithButtons()
                    ->columnSpanFull(),
                CheckboxList::make('types')
                    ->label('Show under these Safari page filters')
                    ->options(config('packages.types'))
                    ->columns(3)
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->label('Feature on the home page'),
                Toggle::make('is_published')
                    ->label('Show on website')
                    ->default(true),
            ]);
    }

    protected static function overviewTab(): Tab
    {
        return Tab::make('Overview & facts')
            ->icon('heroicon-o-document-text')
            ->schema([
                TextInput::make('location')
                    ->maxLength(255)
                    ->placeholder('e.g. Serengeti & Ngorongoro, Tanzania'),
                Textarea::make('overview')
                    ->label('Package overview')
                    ->rows(8)
                    ->helperText('Separate paragraphs with a blank line. Leave empty to use the summary.'),
                KeyValue::make('facts')
                    ->label('Price card facts')
                    ->keyLabel('Label')
                    ->valueLabel('Value')
                    ->addActionLabel('Add fact')
                    ->reorderable()
                    ->helperText('e.g. Duration: 5 Days · 4 Nights, Group size: Private, Best time: June – October.'),
            ]);
    }

    protected static function itineraryTab(): Tab
    {
        return Tab::make('Itinerary')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                Repeater::make('itinerary')
                    ->hiddenLabel()
                    ->itemLabel(fn (array $state, ?int $index) => 'Day '.(($index ?? 0) + 1).' • '.($state['title'] ?? 'New day'))
                    ->addActionLabel('Add day')
                    ->collapsible()
                    ->collapsed()
                    ->cloneable()
                    ->reorderableWithButtons()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Arusha to the Serengeti'),
                        Textarea::make('text')
                            ->label('What happens this day')
                            ->required()
                            ->rows(4),
                        Grid::make(2)->schema([
                            MediaUpload::make('image', 'packages/itinerary')->label('Day photo'),
                            TextInput::make('caption')->label('Photo caption')->maxLength(80),
                        ]),
                        Textarea::make('note')
                            ->label('Note (optional)')
                            ->rows(2),
                        Fieldset::make('Highlighted activity (optional)')
                            ->statePath('activity')
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')->maxLength(255)->placeholder('e.g. Spice Farm Tour'),
                                MediaUpload::make('image', 'packages/activities')->label('Activity photo'),
                                Textarea::make('text')->label('Description')->rows(3)->columnSpanFull(),
                            ]),
                        Section::make('Overnight & meals')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                TextInput::make('stay')
                                    ->label('Accommodation area')
                                    ->placeholder('e.g. Central Serengeti (leave empty on the last day)'),
                                TextInput::make('meals')
                                    ->label('Meal plan')
                                    ->placeholder('e.g. Full board'),
                                Repeater::make('options')
                                    ->label('Accommodation options')
                                    ->schema([
                                        TextInput::make('level')->required()->placeholder('Comfort'),
                                        TextInput::make('name')->required()->placeholder('Tented camp, Central Serengeti'),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add option')
                                    ->columnSpanFull(),
                                MediaUpload::make('stay_image', 'packages/stays')->label('Accommodation photo'),
                                TextInput::make('stay_caption')->label('Accommodation photo caption')->maxLength(80),
                            ]),
                    ]),
            ]);
    }

    protected static function inclusionsTab(): Tab
    {
        return Tab::make('Included / not included')
            ->icon('heroicon-o-check-circle')
            ->columns(2)
            ->schema([
                Repeater::make('included')
                    ->simple(TextInput::make('item')->required()->maxLength(255))
                    ->addActionLabel('Add item')
                    ->reorderableWithButtons()
                    ->defaultItems(0),
                Repeater::make('excluded')
                    ->label('Not included')
                    ->simple(TextInput::make('item')->required()->maxLength(255))
                    ->addActionLabel('Add item')
                    ->reorderableWithButtons()
                    ->defaultItems(0),
            ]);
    }
}
