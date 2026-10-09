<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Review')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Guest name')
                            ->required()
                            ->maxLength(120)
                            ->helperText('As the guest wants it shown, e.g. "Sarah M.".'),
                        TextInput::make('country')
                            ->maxLength(80),
                        Select::make('rating')
                            ->options([5 => '★★★★★ Excellent', 4 => '★★★★ Very good', 3 => '★★★ Good', 2 => '★★ Fair', 1 => '★ Poor'])
                            ->default(5)
                            ->required(),
                        DatePicker::make('travelled_on')
                            ->label('Travel date')
                            ->native(false)
                            ->displayFormat('M Y'),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Review')
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),
                        MediaUpload::make('photos', 'reviews')
                            ->label('Guest photos')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(6)
                            ->helperText('The first two appear on the review card.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Settings')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                        Select::make('package_id')
                            ->label('Trip')
                            ->relationship('package', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText('Links the review to a package page and its trip type.'),
                        TextInput::make('source_url')
                            ->label('Original review link')
                            ->url()
                            ->maxLength(255)
                            ->helperText('e.g. the review on Google or Tripadvisor.'),
                        Toggle::make('is_sample')
                            ->label('Sample / placeholder')
                            ->helperText('Samples are never shown on the live website. Only publish real reviews from real guests.'),
                    ]),
            ]);
    }
}
