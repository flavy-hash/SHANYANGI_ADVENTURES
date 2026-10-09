<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Article')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Web address')
                            ->prefix('/blog/')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->required()
                            ->rows(2)
                            ->maxLength(300)
                            ->helperText('One or two sentences shown on the article card and at the top of the article.'),
                        Textarea::make('body')
                            ->required()
                            ->rows(22)
                            ->helperText('Leave a blank line between paragraphs. Start a line with "## " to make it a heading, e.g. "## When to go".'),
                    ]),
                Section::make('Publishing')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                        DatePicker::make('published_at')
                            ->label('Publish date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('j M Y')
                            ->helperText('A future date schedules the article: it appears on that day.'),
                        Select::make('category')
                            ->options(config('blog.categories'))
                            ->required(),
                        TextInput::make('read_minutes')
                            ->label('Reading time')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->default(5)
                            ->suffix('min'),
                        MediaUpload::make('image', 'blog')
                            ->label('Cover photo'),
                    ]),
            ]);
    }
}
