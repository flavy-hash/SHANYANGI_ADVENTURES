<?php

namespace App\Filament\Resources\TripRequests\Pages;

use App\Filament\Resources\TripRequests\Tables\TripRequestsTable;
use App\Filament\Resources\TripRequests\TripRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTripRequest extends ViewRecord
{
    protected static string $resource = TripRequestResource::class;

    /**
     * Shown as buttons here; the inquiries list has the same actions in a three-dots menu.
     */
    protected function getHeaderActions(): array
    {
        [$reply, $quote, $toggleHandled, $delete] = TripRequestsTable::inquiryActions();

        return [
            $reply,
            $quote->color('gray'),
            $toggleHandled,
            $delete,
        ];
    }
}
