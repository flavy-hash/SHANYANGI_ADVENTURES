Dear {!! $quote->customer_name !!},

Thank you for your interest in travelling with Shanyangi Adventures.

Please find attached your quotation {!! $quote->reference !!}@if ($quote->package) for the {!! $quote->package->title !!}@endif.

Total: {!! \App\Models\Quote::money($quote->total) !!}
@if ($quote->valid_until)
Valid until: {!! $quote->valid_until->format('j F Y') !!}
@endif

If you have any questions or would like to make changes, simply reply to this email quoting {!! $quote->reference !!}.

Kind regards,
The Shanyangi Adventures team
{!! rtrim(config('app.url'), '/') !!}
