<?php

namespace App\Filament\Resources\NewsletterSubscribers;

use App\Filament\Resources\NewsletterSubscribers\Pages\ManageNewsletterSubscribers;
use App\Models\NewsletterSubscriber;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $modelLabel = 'subscriber';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static string|UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (string $state) => mb_strtolower(trim($state))),
                TextInput::make('source')
                    ->default('admin')
                    ->maxLength(50)
                    ->helperText('Where they signed up, e.g. "footer" or "admin".'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->state(fn (NewsletterSubscriber $record) => $record->unsubscribed_at ? 'Unsubscribed' : 'Active')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Active' ? 'success' : 'gray'),
                TextColumn::make('source')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Subscribed')
                    ->date('j M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('unsubscribed_at')
                    ->label('Status')
                    ->nullable()
                    ->placeholder('All subscribers')
                    ->trueLabel('Unsubscribed')
                    ->falseLabel('Active'),
            ])
            ->recordActions([
                Action::make('toggleSubscription')
                    ->label(fn (NewsletterSubscriber $record) => $record->unsubscribed_at ? 'Resubscribe' : 'Unsubscribe')
                    ->icon(fn (NewsletterSubscriber $record) => $record->unsubscribed_at ? Heroicon::OutlinedArrowUturnLeft : Heroicon::OutlinedNoSymbol)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (NewsletterSubscriber $record) => $record->update(['unsubscribed_at' => $record->unsubscribed_at ? null : now()])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No subscribers yet')
            ->emptyStateDescription('People who sign up with the "Stay Updated" form on the website appear here.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNewsletterSubscribers::route('/'),
        ];
    }
}
