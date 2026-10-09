<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('travelled_on', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Guest')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Review $record) => $record->country),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('package.title')
                    ->label('Trip')
                    ->placeholder('-')
                    ->limit(30),
                TextColumn::make('travelled_on')
                    ->label('Travelled')
                    ->date('M Y')
                    ->sortable(),
                TextColumn::make('is_sample')
                    ->label('Type')
                    ->state(fn (Review $record) => $record->is_sample ? 'Sample' : 'Real')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Sample' ? 'danger' : 'success'),
                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('is_sample')->label('Sample placeholders'),
                SelectFilter::make('rating')
                    ->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']),
                SelectFilter::make('package')
                    ->relationship('package', 'title')
                    ->label('Trip'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
