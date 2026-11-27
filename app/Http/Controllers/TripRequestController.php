<?php

namespace App\Http\Controllers;

use App\Filament\Resources\TripRequests\TripRequestResource;
use App\Mail\TripRequestReceived;
use App\Models\TripRequest;
use App\Support\AdminAlerts;
use App\Support\BookingEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TripRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field. Pretend success, store nothing.
        if (filled($request->input('website'))) {
            return $this->success();
        }

        try {
            $data = $this->validated($request);
        } catch (ValidationException $e) {
            // Land back on the form (not the top of the page) so the errors are visible.
            throw $e->redirectTo(route('contact').'#request');
        }

        $tripRequest = TripRequest::create([
            ...$data,
            'children' => $data['children'] ?? 0,
            'interests' => array_values($data['interests'] ?? []),
        ]);

        // Notify the office by email and with a bell alert in the admin.
        BookingEmails::send(new TripRequestReceived($tripRequest), ['trip_request_id' => $tripRequest->id]);

        AdminAlerts::send(
            'New trip request from '.$tripRequest->name,
            collect([$tripRequest->trip, $tripRequest->travel_date?->format('j M Y'), $tripRequest->adults.' adult(s)'])->filter()->implode(' · '),
            TripRequestResource::getUrl('view', ['record' => $tripRequest], panel: 'admin'),
            'heroicon-o-paper-airplane',
        );

        return $this->success($tripRequest->name);
    }

    protected function validated(Request $request): array
    {
        return $request->validateWithBag('tripRequest', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:80'],
            'trip' => ['nullable', 'string', 'max:150'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:60'],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['nullable', 'integer', 'min:0', 'max:50'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', Rule::in(array_keys(TripRequest::INTERESTS))],
            'budget' => ['nullable', 'string', Rule::in(array_keys(TripRequest::BUDGETS))],
            'message' => ['nullable', 'string', 'max:3000'],
        ], [
            'travel_date.after_or_equal' => 'Please choose a date from today onwards.',
        ]);
    }

    protected function success(?string $name = null): RedirectResponse
    {
        return redirect()->to(route('contact').'#request')
            ->with('trip_request_sent', $name ?: true);
    }
}
