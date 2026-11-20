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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
                // Itinerary progress: days written out of the planned number of days.
                TextColumn::make('completion')
                    ->label('Itinerary')
                    ->state(fn (Package $record) => $record->completion['added'].' / '.$record->completion['planned'].' days')
                    ->badge()
                    ->color(fn (Package $record) => $record->completion['left'] === 0 ? 'success' : ($record->completion['percent'] >= 50 ? 'warning' : 'danger'))
                    ->description(fn (Package $record) => $record->completion['left'] === 0
                        ? 'Complete'
                        : $record->completion['percent'].'% · '.$record->completion['left'].' '.($record->completion['left'] === 1 ? 'day' : 'days').' left'),
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
                Filter::make('incomplete')
                    ->label('Itinerary not finished')
                    ->query(fn (Builder $query) => $query->whereRaw('planned_days > '.(DB::getDriverName() === 'sqlite' ? 'json_array_length(itinerary)' : 'JSON_LENGTH(itinerary)'))),
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
