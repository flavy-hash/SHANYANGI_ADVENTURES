<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Models\Package;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->disk('media')
                    ->visibility('private')
                    ->square()
                    ->imageSize(56),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Package $record) => $record->tagline),
                TextColumn::make('category')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('days')
                    ->state(fn (Package $record) => count($record->itinerary ?? []).' days'),
                TextColumn::make('price')
                    ->formatStateUsing(fn (?int $state) => config('packages.currency', '$').number_format($state))
                    ->placeholder('On request')
                    ->sortable(),
                ToggleColumn::make('is_featured')
                    ->label('Home page'),
                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(fn () => Package::query()->distinct()->orderBy('category')->pluck('category', 'category')->all()),
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('is_featured')->label('On home page'),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('viewOnSite')
                        ->label('View on website')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->url(fn (Package $record) => route('safaris.show', $record->slug), shouldOpenInNewTab: true)
                        ->visible(fn (Package $record) => $record->is_published),
                    ReplicateAction::make()
                        ->label('Duplicate')
                        ->modalDescription('Creates an unpublished copy you can edit into a new package.')
                        ->mutateRecordDataUsing(function (array $data) {
                            $data['title'] .= ' (copy)';
                            $data['slug'] .= '-copy-'.now()->format('His');
                            $data['is_published'] = false;
                            $data['is_featured'] = false;

                            return $data;
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
