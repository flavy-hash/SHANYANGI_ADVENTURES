<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->weight('bold')
                    ->fontFamily('mono'),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn (Quote $record) => $record->customer_email),
                TextColumn::make('package.title')
                    ->label('Package')
                    ->placeholder('Tailor-made')
                    ->limit(30),
                TextColumn::make('total')
                    ->state(fn (Quote $record) => Quote::money($record->total)),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state) => Quote::STATUSES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state) => ['draft' => 'gray', 'sent' => 'info', 'accepted' => 'success', 'declined' => 'danger'][$state] ?? 'gray'),
                TextColumn::make('source')
                    ->formatStateUsing(fn (string $state) => $state === 'website' ? 'Website' : 'Admin')
                    ->badge()
                    ->color(fn (string $state) => $state === 'website' ? 'warning' : 'gray'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('j M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Quote::STATUSES),
                SelectFilter::make('source')->options(['admin' => 'Admin', 'website' => 'Instant (website)']),
            ])
            ->recordActions([
                QuoteResource::downloadAction()->iconButton()->tooltip('Download PDF'),
                ActionGroup::make([
                    EditAction::make(),
                    QuoteResource::emailAction(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No quotes yet')
            ->emptyStateDescription('Create one here or from an inquiry. Instant quotes from package pages also appear here.');
    }
}
