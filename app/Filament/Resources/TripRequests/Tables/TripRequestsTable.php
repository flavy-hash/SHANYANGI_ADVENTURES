<?php

namespace App\Filament\Resources\TripRequests\Tables;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\TripRequest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TripRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->state(fn (TripRequest $record) => $record->handled_at ? 'Handled' : 'New')
                    ->badge()
                    ->color(fn (string $state) => $state === 'New' ? 'warning' : 'success'),
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (TripRequest $record) => $record->email),
                TextColumn::make('trip')
                    ->label('Trip of interest')
                    ->placeholder('Not specified')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('travel_date')
                    ->date('j M Y')
                    ->placeholder('Flexible')
                    ->sortable(),
                TextColumn::make('travellers')
                    ->state(fn (TripRequest $record) => $record->adults.' adult'.($record->adults === 1 ? '' : 's')
                        .($record->children ? ', '.$record->children.' child'.($record->children === 1 ? '' : 'ren') : '')),
                TextColumn::make('budget')
                    ->formatStateUsing(fn (?string $state) => TripRequest::BUDGETS[$state] ?? $state)
                    ->badge()
                    ->color('gray')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('country')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->dateTimeTooltip('j M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('handled_at')
                    ->label('Status')
                    ->nullable()
                    ->placeholder('All inquiries')
                    ->trueLabel('Handled')
                    ->falseLabel('New'),
                SelectFilter::make('budget')
                    ->options(TripRequest::BUDGETS),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make(self::inquiryActions())->tooltip('More actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markHandled')
                        ->label('Mark as handled')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(fn (Collection $records) => $records->each->update(['handled_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No inquiries yet')
            ->emptyStateDescription('Trip requests sent from the website\'s contact page will appear here.');
    }

    /**
     * The three-dots menu items, shared by the list and the inquiry page.
     *
     * @return list<Action|DeleteAction>
     */
    public static function inquiryActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply by email')
                ->icon(Heroicon::OutlinedEnvelope)
                ->url(fn (TripRequest $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Your trip request: '.($record->trip ?: 'Tanzania'))),
            Action::make('quote')
                ->label('Create quote')
                ->icon(Heroicon::OutlinedDocumentCurrencyDollar)
                ->url(fn (TripRequest $record) => QuoteResource::getUrl('create', ['trip_request' => $record->id])),
            self::toggleHandledAction(),
            DeleteAction::make(),
        ];
    }

    public static function toggleHandledAction(): Action
    {
        return Action::make('toggleHandled')
            ->label(fn (TripRequest $record) => $record->handled_at ? 'Reopen' : 'Mark handled')
            ->icon(fn (TripRequest $record) => $record->handled_at ? Heroicon::OutlinedArrowUturnLeft : Heroicon::OutlinedCheckCircle)
            ->color(fn (TripRequest $record) => $record->handled_at ? 'gray' : 'success')
            ->action(fn (TripRequest $record) => $record->update(['handled_at' => $record->handled_at ? null : now()]));
    }
}
