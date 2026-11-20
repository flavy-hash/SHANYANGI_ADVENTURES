<?php

namespace App\Filament\Resources\Accommodations;

use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Filament\Support\MediaUpload;
use App\Models\Accommodation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Lodges, camps and hotels listed on the Accommodation page (/accommodations),
 * each with its own page (/accommodations/{slug}) that itineraries link to.
 */
class AccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static ?string $modelLabel = 'accommodation';

    protected static ?string $pluralModelLabel = 'accommodations';

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Serengeti Safari Camp')
                    ->helperText('Itinerary accommodation options with this exact name (ignoring "or similar") link to this stay automatically.'),
                TextInput::make('slug')
                    ->label('Web address')
                    ->prefix('/accommodations/')
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Filled in from the name')
                    ->helperText('Leave empty to create it from the name. Changing it breaks old links.'),
                TextInput::make('location')
                    ->required()
                    ->maxLength(80)
                    ->datalist(fn () => self::suggestions('location', Accommodation::LOCATIONS))
                    ->helperText('Pick a suggestion or type a new area. Each area gets its own filter button on the website.'),
                TextInput::make('type')
                    ->maxLength(60)
                    ->datalist(fn () => self::suggestions('type', Accommodation::TYPES))
                    ->placeholder('e.g. Tented camp'),
                Select::make('level')
                    ->label('Comfort level')
                    ->options(array_combine(Accommodation::LEVELS, Accommodation::LEVELS)),
                Toggle::make('is_published')
                    ->label('Show on website')
                    ->default(true)
                    ->inline(false),
                Textarea::make('description')
                    ->required()
                    ->rows(7)
                    ->maxLength(5000)
                    ->helperText('The first paragraph also shows on the card. Separate paragraphs with a blank line.')
                    ->columnSpanFull(),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->rules(['url:http,https'])
                    ->maxLength(500)
                    ->prefixIcon(Heroicon::OutlinedGlobeAlt)
                    ->placeholder('https://www.example-lodge.com')
                    ->helperText('The stay\'s own website. "Enquire About This Stay" on its page opens this link in a new tab. Leave empty to use your enquiry form instead.')
                    ->columnSpanFull(),
                TagsInput::make('facilities')
                    ->suggestions(Accommodation::FACILITIES)
                    ->placeholder('e.g. Swimming pool')
                    ->helperText('Short labels shown on the stay page. Press Enter after each one.')
                    ->columnSpanFull(),
                MediaUpload::make('image', 'accommodations')
                    ->label('Photo')
                    ->helperText(fn (?Accommodation $record) => $record && str_starts_with((string) $record->image, 'http')
                        ? 'This stay currently uses an online photo. Upload one to replace it; leave empty to keep the current photo.'
                        : 'A landscape photo works best (it is cropped to fit the card).')
                    ->columnSpanFull(),
                MediaUpload::make('gallery', 'accommodations/gallery')
                    ->label('Photo gallery')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->maxFiles(24)
                    ->panelLayout('grid')
                    ->helperText('More photos for the stay page: rooms, views, dining, pool. Drag to reorder. Visitors can open them full screen.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('media')
                    ->visibility('private')
                    ->square()
                    ->imageSize(56),
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Accommodation $record) => collect([$record->type, $record->level])->filter()->implode(' · ')),
                TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                // Stays created from itineraries start with one photo: add a gallery to complete their page.
                TextColumn::make('photos')
                    ->label('Photos')
                    ->state(fn (Accommodation $record) => count($record->photos))
                    ->badge()
                    ->color(fn (int $state) => $state > 1 ? 'success' : 'warning')
                    ->tooltip(fn (int $state) => $state > 1 ? null : 'Add a photo gallery for this stay\'s page'),
                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->options(fn () => Accommodation::query()->distinct()->orderBy('location')->pluck('location', 'location')->all()),
                SelectFilter::make('level')
                    ->label('Comfort level')
                    ->options(array_combine(Accommodation::LEVELS, Accommodation::LEVELS)),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View page')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Accommodation $record) => $record->is_published && $record->slug ? route('accommodations.show', $record->slug) : null, shouldOpenInNewTab: true)
                    ->visible(fn (Accommodation $record) => $record->is_published && filled($record->slug)),
                EditAction::make()
                    // Keep an existing online photo unless a new one was uploaded.
                    ->mutateDataUsing(fn (array $data, Accommodation $record) => [
                        ...$data,
                        'image' => filled($data['image'] ?? null) ? $data['image'] : (str_starts_with((string) $record->image, 'http') ? $record->image : null),
                    ]),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No accommodation yet')
            ->emptyStateDescription('Add the lodges, camps and hotels you work with. Each gets its own page with a photo gallery, linked from the itineraries.');
    }

    /**
     * Built-in suggestions plus the values already used.
     *
     * @return list<string>
     */
    protected static function suggestions(string $column, array $defaults): array
    {
        return collect($defaults)
            ->merge(Accommodation::query()->whereNotNull($column)->distinct()->pluck($column))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccommodations::route('/'),
        ];
    }
}
