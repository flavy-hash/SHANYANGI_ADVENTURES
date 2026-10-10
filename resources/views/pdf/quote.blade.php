@php
    /** @var \App\Models\Quote $quote */
    use App\Models\Quote;

    $site = config('site');
    $website = preg_replace('#^https?://#', '', rtrim(config('app.url'), '/'));
    $items = $quote->items ?? [];
    $nights = $quote->travel_start && $quote->travel_end ? $quote->travel_start->diffInDays($quote->travel_end) : null;

    // Acacia mark (resources/pdf/logo.png), embedded so the PDF needs no web request.
    $logo = 'data:image/png;base64,'.base64_encode(file_get_contents(resource_path('pdf/logo.png')));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quote->reference }}</title>
    <style>
        @page { margin: 34px 40px 70px; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 10.5px; color: #2b3329; line-height: 1.5; }
        h1, h2, h3, p { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #6b7468; }
        .green { color: #395643; }

        .header td { vertical-align: top; }
        .brand-name { font-size: 17px; font-weight: bold; color: #23331f; letter-spacing: 0.5px; }
        .brand-sub { font-size: 8.5px; letter-spacing: 3px; color: #6b7468; }
        .company { font-size: 9.5px; line-height: 1.55; margin-top: 6px; }
        .doc-title { font-size: 22px; font-weight: bold; color: #23331f; text-align: right; letter-spacing: 1px; }
        .meta { margin-top: 6px; }
        .meta td { text-align: right; padding: 1px 0; font-size: 10px; }
        .meta td.label { color: #6b7468; padding-right: 10px; }
        .rule { height: 3px; background: #395643; margin: 16px 0 18px; }

        .boxes td.box { width: 50%; vertical-align: top; background: #f6f1e7; border-radius: 6px; padding: 12px 14px; }
        .boxes td.gap { width: 14px; }
        .box-title { font-size: 8.5px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #9a5f1e; margin-bottom: 6px; }
        .kv td { padding: 2px 0; vertical-align: top; }
        .kv td.k { color: #6b7468; width: 38%; }
        .kv td.v { font-weight: bold; color: #23331f; }

        .items { margin-top: 20px; }
        .items th { background: #23331f; color: #fff; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; text-align: left; padding: 8px 10px; }
        .items th.num, .items td.num { text-align: right; }
        .items td { padding: 9px 10px; border-bottom: 1px solid #e6dfcf; vertical-align: top; }
        .items tr.alt td { background: #fbf8f2; }

        .totals { width: 46%; margin-left: 54%; margin-top: 12px; }
        .totals td { padding: 5px 10px; }
        .totals td.num { text-align: right; }
        .totals tr.grand td { background: #395643; color: #fff; font-size: 13px; font-weight: bold; padding: 10px; }

        .notes { margin-top: 22px; padding: 12px 14px; border-left: 3px solid #b06e1f; background: #fbf8f2; }
        .terms { margin-top: 18px; font-size: 9px; color: #6b7468; }
        .terms ol { margin: 4px 0 0; padding-left: 16px; }
        .footer { position: fixed; bottom: -44px; left: 0; right: 0; font-size: 8.5px; color: #6b7468; text-align: center; border-top: 1px solid #e6dfcf; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="footer">
        {{ config('app.name') === 'Laravel' ? 'Shanyangi Adventures' : config('app.name') }} · {{ $website }}@if ($site['email']) · {{ $site['email'] }}@endif @if ($site['phone']) · {{ $site['phone'] }}@endif
        · Quotation {{ $quote->reference }}
    </div>

    {{-- Header: company + document details --}}
    <table class="header">
        <tr>
            <td style="width: 55%;">
                <table>
                    <tr>
                        <td style="width: 70px; vertical-align: middle;"><img src="{{ $logo }}" style="width: 60px; height: 44px;" alt=""></td>
                        <td style="vertical-align: middle;">
                            <div class="brand-name">SHANYANGI</div>
                            <div class="brand-sub">ADVENTURES</div>
                        </td>
                    </tr>
                </table>
                <div class="company muted">
                    Tanzania safaris, Kilimanjaro climbs &amp; Zanzibar escapes<br>
                    @if ($site['address']){{ $site['address'] }}<br>@endif
                    @if ($site['email']){{ $site['email'] }}@endif @if ($site['email'] && $site['phone']) · @endif @if ($site['phone']){{ $site['phone'] }}@endif
                    @if ($site['email'] || $site['phone'])<br>@endif
                    {{ $website }}
                </div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">QUOTATION</div>
                <table class="meta">
                    <tr><td class="label">Reference</td><td><strong>{{ $quote->reference }}</strong></td></tr>
                    <tr><td class="label">Date</td><td>{{ $quote->created_at->timezone(config('site.timezone'))->format('j F Y') }}</td></tr>
                    @if ($quote->valid_until)
                        <tr><td class="label">Valid until</td><td>{{ $quote->valid_until->format('j F Y') }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    {{-- Customer + trip --}}
    <table class="boxes">
        <tr>
            <td class="box">
                <div class="box-title">Prepared for</div>
                <table class="kv">
                    <tr><td class="k">Name</td><td class="v">{{ $quote->customer_name }}</td></tr>
                    <tr><td class="k">Email</td><td class="v">{{ $quote->customer_email }}</td></tr>
                    @if ($quote->customer_phone)<tr><td class="k">Phone</td><td class="v">{{ $quote->customer_phone }}</td></tr>@endif
                    @if ($quote->customer_country)<tr><td class="k">Country</td><td class="v">{{ $quote->customer_country }}</td></tr>@endif
                </table>
            </td>
            <td class="gap"></td>
            <td class="box">
                <div class="box-title">Your trip</div>
                <table class="kv">
                    <tr><td class="k">Package</td><td class="v">{{ $quote->package?->title ?? 'Tailor-made journey' }}</td></tr>
                    <tr>
                        <td class="k">Travellers</td>
                        <td class="v">{{ $quote->adults }} {{ str('adult')->plural($quote->adults) }}@if ($quote->children), {{ $quote->children }} {{ $quote->children === 1 ? 'child' : 'children' }}@endif</td>
                    </tr>
                    <tr>
                        <td class="k">Travel dates</td>
                        <td class="v">
                            @if ($quote->travel_start)
                                {{ $quote->travel_start->format('j M Y') }}@if ($quote->travel_end) – {{ $quote->travel_end->format('j M Y') }}@endif
                            @else
                                To be confirmed
                            @endif
                        </td>
                    </tr>
                    @if ($nights)<tr><td class="k">Duration</td><td class="v">{{ $nights + 1 }} days · {{ $nights }} {{ str('night')->plural($nights) }}</td></tr>@endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Itemised costs --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th>Description</th>
                <th class="num" style="width: 10%;">Qty</th>
                <th class="num" style="width: 17%;">Unit price</th>
                <th class="num" style="width: 17%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $i => $item)
                <tr @class(['alt' => $i % 2 === 1])>
                    <td class="muted">{{ $i + 1 }}</td>
                    <td>{{ $item['description'] ?? '' }}</td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) ($item['quantity'] ?? 0), 2), '0'), '.') }}</td>
                    <td class="num">{{ Quote::money($item['unit_price'] ?? 0) }}</td>
                    <td class="num">{{ Quote::money(Quote::lineTotal($item)) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No items.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="num">{{ Quote::money($quote->subtotal) }}</td></tr>
        @if ((float) $quote->discount > 0)
            <tr><td class="muted">Discount</td><td class="num">− {{ Quote::money($quote->discount) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td class="num">{{ Quote::money($quote->total) }}</td></tr>
    </table>

    @if ($quote->notes)
        <div class="notes">
            <div class="box-title">Notes</div>
            {!! nl2br(e($quote->notes)) !!}
        </div>
    @endif

    <div class="terms">
        <strong class="green">Terms</strong>
        <ol>
            @foreach (config('quotes.terms') as $term)
                <li>{{ $term }}</li>
            @endforeach
        </ol>
        <p style="margin-top: 10px;">Thank you for considering Shanyangi Adventures. To accept this quotation or ask a question, simply reply to our email quoting <strong>{{ $quote->reference }}</strong>.</p>
    </div>
</body>
</html>
