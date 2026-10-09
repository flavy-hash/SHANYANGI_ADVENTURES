<?php

namespace App\Filament\Resources\TripRequests;

use App\Filament\Resources\TripRequests\Pages\ListTripRequests;
use App\Filament\Resources\TripRequests\Pages\ViewTripRequest;
use App\Filament\Resources\TripRequests\Schemas\TripRequestInfolist;
use App\Filament\Resources\TripRequests\Tables\TripRequestsTable;
use App\Models\TripRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Trip requests sent from the website's contact form. Read-only: they are
 * reviewed, marked as handled and answered by email.
 */
class TripRequestResource extends Resource
{
    protected static ?string $model = TripRequest::class;

    protected static ?string $modelLabel = 'inquiry';

    protected static ?string $pluralModelLabel = 'inquiries';

    protected static ?string $navigationLabel = 'Inquiries';

    protected static string|UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return TripRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripRequestsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $new = TripRequest::query()->whereNull('handled_at')->count();

        return $new ? (string) $new : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New inquiries';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTripRequests::route('/'),
            'view' => ViewTripRequest::route('/{record}'),
        ];
    }
}
