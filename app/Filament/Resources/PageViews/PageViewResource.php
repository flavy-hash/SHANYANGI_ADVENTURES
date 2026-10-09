<?php

namespace App\Filament\Resources\PageViews;

use App\Filament\Resources\PageViews\Pages\ManagePageViews;
use App\Models\PageView;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Read-only log of anonymous page views (App\Http\Middleware\LogPageView).
 */
class PageViewResource extends Resource
{
    protected static ?string $model = PageView::class;

    protected static ?string $modelLabel = 'page view';

    protected static ?string $navigationLabel = 'Visitor activity';

    protected static string|UnitEnum|null $navigationGroup = 'Insights';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->description('Anonymous: no cookies, IP addresses or personal details are stored. A visitor ID only groups views from the same visitor on the same day. Records older than '.config('analytics.retention_days').' days are deleted automatically.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('j M Y, H:i')
                    ->sortable(),
                TextColumn::make('page_type')
                    ->label('Page')
                    ->formatStateUsing(fn (string $state) => PageView::PAGE_TYPES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state) => $state === 'package' ? 'primary' : 'gray')
                    ->description(fn (PageView $record) => $record->path),
                TextColumn::make('package.title')
                    ->label('Tour viewed')
                    ->placeholder('-')
                    ->limit(35),
                TextColumn::make('device')
                    ->badge()
                    ->color('gray')
                    ->icon(fn (string $state) => match ($state) {
                        'mobile' => Heroicon::OutlinedDevicePhoneMobile,
                        'tablet' => Heroicon::OutlinedDeviceTablet,
                        default => Heroicon::OutlinedComputerDesktop,
                    }),
                TextColumn::make('referrer_host')
                    ->label('Came from')
                    ->placeholder('Direct / internal'),
                TextColumn::make('visitor_hash')
                    ->label('Visitor ID')
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (string $state) => substr($state, 0, 8))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
                SelectFilter::make('page_type')
                    ->label('Page')
                    ->options(PageView::PAGE_TYPES),
                SelectFilter::make('package')
                    ->label('Tour')
                    ->relationship('package', 'title'),
                SelectFilter::make('device')
                    ->options(['mobile' => 'Mobile', 'tablet' => 'Tablet', 'desktop' => 'Desktop']),
            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No visits recorded yet')
            ->emptyStateDescription('Page views from website visitors appear here (logged-in staff are not counted).');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePageViews::route('/'),
        ];
    }
}
