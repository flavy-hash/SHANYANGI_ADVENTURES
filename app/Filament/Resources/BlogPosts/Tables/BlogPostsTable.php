<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Models\BlogPost;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->disk('media')
                    ->visibility('private')
                    ->square()
                    ->imageSize(56),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->limit(60)
                    ->description(fn (BlogPost $record) => $record->read_minutes.' min read'),
                TextColumn::make('category')
                    ->formatStateUsing(fn (string $state) => config('blog.categories')[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->state(fn (BlogPost $record) => match (true) {
                        ! $record->is_published => 'Draft',
                        $record->published_at->isFuture() => 'Scheduled',
                        default => 'Published',
                    })
                    ->badge()
                    ->color(fn (string $state) => ['Published' => 'success', 'Scheduled' => 'info', 'Draft' => 'gray'][$state]),
                TextColumn::make('published_at')
                    ->label('Publish date')
                    ->date('j M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(config('blog.categories')),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('viewOnSite')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (BlogPost $record) => route('blog.show', $record->slug), shouldOpenInNewTab: true)
                    ->visible(fn (BlogPost $record) => $record->is_published && ! $record->published_at->isFuture()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
