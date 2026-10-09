<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Package;
use App\Models\TripRequest;
use App\Support\QuotePdf;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Url;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    /** Inquiry to pre-fill from (?trip_request=ID), set by "Create quote" on an inquiry. */
    #[Url(as: 'trip_request')]
    public ?string $tripRequestId = null;

    protected function fillForm(): void
    {
        parent::fillForm();

        $inquiry = $this->tripRequestId ? TripRequest::find($this->tripRequestId) : null;
        if (! $inquiry) {
            return;
        }

        $package = $inquiry->trip ? Package::query()->where('title', $inquiry->trip)->first() : null;

        $this->form->fill([
            ...$this->form->getRawState(),
            'trip_request_id' => $inquiry->id,
            'customer_name' => $inquiry->name,
            'customer_email' => $inquiry->email,
            'customer_phone' => $inquiry->phone,
            'customer_country' => $inquiry->country,
            'adults' => $inquiry->adults,
            'children' => $inquiry->children,
            'travel_start' => $inquiry->travel_date?->toDateString(),
            'travel_end' => $inquiry->travel_date && $inquiry->duration_days ? $inquiry->travel_date->copy()->addDays($inquiry->duration_days - 1)->toDateString() : null,
            'package_id' => $package?->id,
            'items' => QuotePdf::packageItems($package, $inquiry->adults, $inquiry->children),
            'notes' => $inquiry->trip && ! $package ? 'Trip requested: '.$inquiry->trip : null,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Link the quote to the inquiry it was created from.
        if ($this->tripRequestId && TripRequest::whereKey($this->tripRequestId)->exists()) {
            $data['trip_request_id'] = (int) $this->tripRequestId;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
