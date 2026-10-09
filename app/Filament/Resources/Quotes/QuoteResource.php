<?php

namespace App\Filament\Resources\Quotes;

use App\Filament\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Resources\Quotes\Pages\EditQuote;
use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Filament\Resources\Quotes\Schemas\QuoteForm;
use App\Filament\Resources\Quotes\Tables\QuotesTable;
use App\Mail\QuoteMail;
use App\Models\Quote;
use App\Support\QuotePdf;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

/**
 * Price quotations with PDF download and email.
 */
class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return QuoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotesTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'customer_name', 'customer_email'];
    }

    public static function downloadAction(): Action
    {
        return Action::make('pdf')
            ->label('PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('gray')
            ->action(fn (Quote $record) => response()->streamDownload(
                function () use ($record) {
                    echo QuotePdf::output($record);
                },
                $record->pdfFilename(),
                ['Content-Type' => 'application/pdf'],
            ));
    }

    public static function emailAction(): Action
    {
        return Action::make('email')
            ->label('Email to customer')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->requiresConfirmation()
            ->modalDescription(fn (Quote $record) => 'Sends the PDF to '.$record->customer_email.' and marks the quote as sent.')
            ->action(function (Quote $record) {
                try {
                    Mail::to($record->customer_email, $record->customer_name)->send(new QuoteMail($record));
                } catch (Throwable $e) {
                    Log::error('Quote email failed', ['quote' => $record->reference, 'error' => $e->getMessage()]);
                    Notification::make()->title('Email could not be sent')->body('Check the mail settings in .env. You can still download the PDF and send it yourself.')->danger()->send();

                    return;
                }

                if ($record->status === 'draft') {
                    $record->update(['status' => 'sent']);
                }

                Notification::make()->title('Quote sent to '.$record->customer_email)->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }
}
