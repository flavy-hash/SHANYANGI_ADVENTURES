<?php

namespace App\Filament\Resources\TripRequests\Schemas;

use App\Models\TripRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Trip')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('trip')
                            ->label('Trip of interest')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                        TextEntry::make('interests')
                            ->formatStateUsing(fn (?string $state) => TripRequest::INTERESTS[$state] ?? $state)
                            ->badge()
                            ->placeholder('None selected')
                            ->columnSpanFull(),
                        TextEntry::make('travel_date')->date('l, j F Y')->placeholder('Flexible'),
                        TextEntry::make('duration_days')->label('Duration')->suffix(' days')->placeholder('Not specified'),
                        TextEntry::make('adults'),
                        TextEntry::make('children'),
                        TextEntry::make('budget')
                            ->label('Budget per person')
                            ->formatStateUsing(fn (?string $state) => TripRequest::BUDGETS[$state] ?? $state)
                            ->placeholder('Not specified'),
                        TextEntry::make('message')
                            ->placeholder('No message')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'whitespace-pre-line']),
                    ]),
                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Contact')
                            ->schema([
                                TextEntry::make('name')->weight('bold'),
                                TextEntry::make('email')
                                    ->copyable()
                                    ->url(fn (TripRequest $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Your trip request: '.($record->trip ?: 'Tanzania'))),
                                TextEntry::make('phone')
                                    ->label('Phone / WhatsApp')
                                    ->copyable()
                                    ->placeholder('-'),
                                TextEntry::make('country')->label('Country of residence')->placeholder('-'),
                            ]),
                        Section::make('Status')
                            ->schema([
                                TextEntry::make('status')
                                    ->state(fn (TripRequest $record) => $record->handled_at ? 'Handled' : 'New')
                                    ->badge()
                                    ->color(fn (string $state) => $state === 'New' ? 'warning' : 'success'),
                                TextEntry::make('created_at')->label('Received')->dateTime('j M Y, H:i'),
                                TextEntry::make('handled_at')->label('Handled on')->dateTime('j M Y, H:i')->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}
