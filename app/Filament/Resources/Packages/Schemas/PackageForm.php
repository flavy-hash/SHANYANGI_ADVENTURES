<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Filament\Support\MediaUpload;
use App\Models\Accommodation;
use App\Models\Package;
use App\Support\RouteMap;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
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
                Select::make('style')
                    ->options(config('packages.styles'))
                    ->placeholder('Not set')
                    ->helperText('Comfort level for the Style filter on the Safari page.'),
                TextInput::make('planned_days')
                    ->label('Number of days')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(60)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('days')
                    ->helperText('How long the trip is. The Itinerary tab shows how many days are still missing.'),
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
                    ->disabled(fn (Get $get) => filled(array_filter(array_column($get('price_tiers') ?? [], 'price'))))
                    ->dehydrated()
                    ->helperText('Leave empty to show "Price on request". With group prices below, this is set to the lowest of them.'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first. You can also drag rows in the list.'),
                Repeater::make('price_tiers')
                    ->label('Group prices (optional)')
                    ->helperText('Price per person by group size, e.g. 2 people $3,771 · 4 people $3,303 · 6 people $3,146. Quotes use the price for the group size; a group between two sizes pays the smaller size\'s price.')
                    ->schema([
                        TextInput::make('people')->label('Group size (people)')->numeric()->integer()->minValue(1)->maxValue(50)->required(),
                        TextInput::make('price')->label('Price per person')->numeric()->integer()->minValue(1)->prefix(config('packages.currency', '$'))->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Add group price')
                    ->reorderable(false)
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('child_price')
                    ->label('Child price (per child, optional)')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->prefix(config('packages.currency', '$'))
                    ->helperText('Leave empty to charge children the adult price.'),
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
        $completion = fn (Get $get) => Package::completionFor((int) $get('planned_days'), count($get('itinerary') ?? []));

        return Tab::make('Itinerary')
            ->icon('heroicon-o-calendar-days')
            ->badge(fn (Get $get) => ($c = $completion($get))['planned'] ? $c['added'].'/'.$c['planned'] : null)
            ->badgeColor(fn (Get $get) => $completion($get)['left'] === 0 ? 'success' : 'warning')
            ->schema([
                // How much of the itinerary is written: e.g. "4 of 6 days added · 66% complete · 2 days left".
                View::make('filament.forms.package-progress')
                    ->viewData(fn (Get $get) => ['completion' => $completion($get)]),
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
                        Grid::make(4)->schema([
                            Select::make('map_place')
                                ->label('Map location')
                                ->options(RouteMap::options())
                                ->default(RouteMap::AUTO)
                                ->placeholder('Automatic (from the day title)')
                                ->selectablePlaceholder(false)
                                ->searchable()
                                ->live()
                                ->helperText(fn (Get $get) => in_array($get('map_place') ?? '', ['', RouteMap::AUTO], true)
                                    ? 'Pinned at: '.(RouteMap::placeFor(['title' => $get('title'), 'caption' => $get('caption'), 'stay' => $get('stay')])['name'] ?? 'no place recognised, please choose one')
                                    : 'Where this day is pinned on the route map.')
                                ->columnSpan(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM ? 1 : 4),
                            TextInput::make('map_name')
                                ->label('Place name')
                                ->placeholder('e.g. Tarangire Safari Lodge')
                                ->maxLength(80)
                                ->visible(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM),
                            TextInput::make('map_lat')
                                ->label('Latitude')
                                ->numeric()
                                ->minValue(-90)
                                ->maxValue(90)
                                ->placeholder('-3.3869')
                                ->required(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM)
                                ->visible(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM),
                            TextInput::make('map_lng')
                                ->label('Longitude')
                                ->numeric()
                                ->minValue(-180)
                                ->maxValue(180)
                                ->placeholder('36.6830')
                                ->helperText('Tip: right-click a spot in Google Maps to copy its coordinates.')
                                ->required(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM)
                                ->visible(fn (Get $get) => $get('map_place') === RouteMap::CUSTOM),
                        ]),
                        // Click / drag on a map to set the point (fills in the coordinates above).
                        ViewField::make('map_picker')
                            ->hiddenLabel()
                            ->view('filament.forms.map-picker')
                            ->dehydrated(false)
                            ->visible(fn (Get $get) => ($get('map_place') ?? '') !== RouteMap::NONE),
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
                                // Pick a stay from Website Content → Accommodations: fills the photo,
                                // the photo caption (its name) and, if empty, the accommodation area.
                                Select::make('accommodation_id')
                                    ->label('Accommodation')
                                    ->placeholder('Search your accommodations…')
                                    ->options(fn () => Accommodation::query()->ordered()->get()
                                        ->mapWithKeys(fn (Accommodation $stay) => [$stay->id => $stay->name.' · '.$stay->location.($stay->is_published ? '' : ' (hidden)')])
                                        ->all())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                                        $stay = $state ? Accommodation::find($state) : null;
                                        if (! $stay) {
                                            return;
                                        }

                                        $set('stay_caption', Str::limit($stay->name, 80, ''));
                                        if (blank($get('stay'))) {
                                            $set('stay', $stay->location);
                                        }
                                        // Uploaded photos are files on the media disk; online photos can't go in the upload field.
                                        if (filled($stay->image) && ! str_starts_with($stay->image, 'http')) {
                                            $set('stay_image', [(string) Str::uuid() => $stay->image]);
                                        }
                                    })
                                    ->helperText('Choosing one fills in the photo and caption below, and links the accommodation and photo to its page on the website. Add new places under Website Content → Accommodations.')
                                    ->columnSpanFull(),
                                TextInput::make('stay')
                                    ->label('Accommodation area')
                                    ->placeholder('e.g. Central Serengeti (leave empty on the last day)'),
                                TextInput::make('meals')
                                    ->label('Meal plan')
                                    ->placeholder('e.g. Full board'),
                                Repeater::make('options')
                                    ->label('Accommodation options')
                                    ->helperText('Link an option to one of your accommodations and its name opens that stay\'s page (description and photo gallery) on the website.')
                                    ->schema([
                                        TextInput::make('level')->required()->placeholder('Comfort'),
                                        TextInput::make('name')->required()->placeholder('Tented camp, Central Serengeti'),
                                        Select::make('accommodation_id')
                                            ->label('Links to')
                                            ->placeholder('Match by name')
                                            ->options(fn () => Accommodation::query()->ordered()->get()
                                                ->mapWithKeys(fn (Accommodation $stay) => [$stay->id => $stay->name.' · '.$stay->location.($stay->is_published ? '' : ' (hidden)')])
                                                ->all())
                                            ->searchable()
                                            ->live()
                                            ->afterStateUpdated(function (?string $state, Set $set) {
                                                if ($stay = $state ? Accommodation::find($state) : null) {
                                                    $set('name', $stay->name);
                                                }
                                            }),
                                    ])
                                    ->columns(3)
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
                Repeater::make('optional_activities')
                    ->label('Optional activities')
                    ->helperText('Extras guests can add at their own cost, shown in their own section below the itinerary.')
                    ->schema([
                        TextInput::make('name')->label('Activity')->required()->maxLength(120)->placeholder('Hot air balloon ride (Serengeti)'),
                        TextInput::make('price')->maxLength(80)->placeholder('USD 500 per person'),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add optional activity')
                    ->reorderableWithButtons()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }
}
