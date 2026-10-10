A visitor generated an instant quote on the website. The PDF they downloaded is attached.

Reference: {!! $quote->reference !!}
Package:   {!! $quote->package?->title !!}
Name:      {!! $quote->customer_name !!}
Email:     {!! $quote->customer_email !!}
@if ($quote->customer_phone)
Phone:     {!! $quote->customer_phone !!}
@endif
@if ($quote->customer_country)
Country:   {!! $quote->customer_country !!}
@endif
Travel:    {!! $quote->travel_start?->format('j M Y') !!}
Travellers: {!! $quote->adults !!} {!! \Illuminate\Support\Str::plural('adult', $quote->adults) !!}@if ($quote->children), {!! $quote->children !!} {!! \Illuminate\Support\Str::plural('child', $quote->children) !!}@endif

Total:     {!! \App\Models\Quote::money($quote->total) !!}

Reply to this email to contact the customer, or open the quote in the admin to adjust and send a final version:
{!! \App\Filament\Resources\Quotes\QuoteResource::getUrl('edit', ['record' => $quote], panel: 'admin') !!}
