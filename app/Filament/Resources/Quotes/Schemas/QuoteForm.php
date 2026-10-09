<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Package;
use App\Models\Quote;
use App\Support\QuotePdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Customer')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_name')->label('Name')->required()->maxLength(120),
                        TextInput::make('customer_email')->label('Email')->email()->required()->maxLength(255),
                        TextInput::make('customer_phone')->label('Phone / WhatsApp')->tel()->maxLength(40),
                        TextInput::make('customer_country')->label('Country')->maxLength(80),
                    ]),
                Section::make('Quote')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('reference')
                            ->state(fn (?Quote $record) => $record?->reference ?? 'Assigned when saved')
                            ->weight('bold'),
                        Select::make('status')
                            ->options(Quote::STATUSES)
                            ->default('draft')
                            ->required(),
                        DatePicker::make('valid_until')
                            ->default(now()->addDays(config('quotes.validity_days')))
                            ->native(false)
                            ->displayFormat('j M Y'),
                    ]),
                Section::make('Trip')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Select::make('package_id')
                            ->label('Package')
                            ->relationship('package', 'title')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText('Choosing a package fills in the price lines below (if they are empty).')
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (empty($get('items'))) {
                                    $set('items', QuotePdf::packageItems(Package::find($state), (int) $get('adults'), (int) $get('children')));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('adults')->numeric()->minValue(1)->maxValue(50)->default(2)->required(),
                        TextInput::make('children')->numeric()->minValue(0)->maxValue(50)->default(0)->required(),
                        DatePicker::make('travel_start')->label('Travel from')->native(false)->displayFormat('j M Y'),
                        DatePicker::make('travel_end')->label('Travel until')->native(false)->displayFormat('j M Y')->afterOrEqual('travel_start'),
                    ]),
                Section::make('Totals')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('discount')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix(config('quotes.currency_symbol'))
                            ->live(onBlur: true),
                        TextEntry::make('subtotal_preview')
                            ->label('Subtotal')
                            ->state(fn (Get $get) => Quote::money(self::subtotal($get('items')))),
                        TextEntry::make('total_preview')
                            ->label('Total')
                            ->weight('bold')
                            ->size('lg')
                            ->state(fn (Get $get) => Quote::money(max(0, self::subtotal($get('items')) - (float) $get('discount')))),
                    ]),
                Section::make('Itemised costs')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('description')->required()->maxLength(255)->columnSpan(4),
                                TextInput::make('quantity')->numeric()->minValue(0)->default(1)->required()->live(onBlur: true),
                                TextInput::make('unit_price')
                                    ->label('Unit price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix(config('quotes.currency_symbol'))
                                    ->required()
                                    ->live(onBlur: true),
                            ])
                            ->columns(6)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->addActionLabel('Add line')
                            ->live(),
                    ]),
                Textarea::make('notes')
                    ->rows(3)
                    ->helperText('Printed on the quote, e.g. what is included or special arrangements.')
                    ->columnSpanFull(),
            ]);
    }

    protected static function subtotal(?array $items): float
    {
        return round(collect($items ?? [])->sum(fn ($item) => Quote::lineTotal((array) $item)), 2);
    }
}
