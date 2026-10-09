<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TripRequests\TripRequestResource;
use App\Models\TripRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * The most recent trip requests, newest first.
 */
class LatestInquiries extends TableWidget
{
    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest inquiries';

    public function table(Table $table): Table
    {
        return $table
            ->query(TripRequest::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('status')
                    ->state(fn (TripRequest $record) => $record->handled_at ? 'Handled' : 'New')
                    ->badge()
                    ->color(fn (string $state) => $state === 'New' ? 'warning' : 'success'),
                TextColumn::make('name')
                    ->weight('bold')
                    ->description(fn (TripRequest $record) => $record->email),
                TextColumn::make('trip')
                    ->label('Trip of interest')
                    ->placeholder('Not specified')
                    ->limit(35),
                TextColumn::make('travel_date')
                    ->date('j M Y')
                    ->placeholder('Flexible'),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since(),
            ])
            ->recordUrl(fn (TripRequest $record) => TripRequestResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('all')
                    ->label('View all')
                    ->link()
                    ->url(TripRequestResource::getUrl()),
            ])
            ->emptyStateHeading('No inquiries yet')
            ->emptyStateDescription('Trip requests from the website will appear here.');
    }
}
