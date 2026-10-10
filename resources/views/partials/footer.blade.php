@php
    $explore = collect(config('navigation.primary', []))
        ->reject(fn ($item) => ($item['path'] ?? '/') === '/' || in_array($item['label'], ['About Us', 'Contacts'], true))
        ->map(fn ($item) => ['label' => $item['label'], 'path' => $item['path']]);

    $company = [
        ['label' => 'About Us', 'path' => '/about-us'],
        ['label' => 'Our Team', 'path' => '/about-us#team'],
        ['label' => 'Reviews', 'path' => '/reviews'],
        ['label' => 'Contact Us', 'path' => '/contact-us'],
        ['label' => 'Send a Request', 'path' => '/contact-us#request'],
    ];

    $whatsapp = preg_replace('/\D/', '', (string) config('site.whatsapp'));
    $phone = preg_replace('/[^\d+]/', '', (string) config('site.phone'));
    $email = config('site.email');
    $address = config('site.address');
@endphp

<footer class="tm-footer" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Site footer</h2>

    <div class="tm-container">
        <div class="tm-footer-grid">
            <div class="tm-footer-brand">
                <a href="{{ url('/') }}" class="inline-flex" aria-label="Shanyangi Adventures home">
                    @include('partials.logo')
                </a>
                <p class="tm-footer-about">
                    Locally owned Tanzanian safaris, Kilimanjaro climbs and Zanzibar escapes, planned around you.
                </p>

                <x-social-links class="tm-footer-social" />
            </div>

            <nav aria-label="Explore">
                <h3 class="tm-footer-heading">Explore</h3>
                <ul class="tm-footer-links">
                    @foreach ($explore as $link)
                        <li><a href="{{ url($link['path']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <nav aria-label="Company">
                <h3 class="tm-footer-heading">Company</h3>
                <ul class="tm-footer-links">
                    @foreach ($company as $link)
                        <li><a href="{{ url($link['path']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div>
                <h3 class="tm-footer-heading">Get in Touch</h3>
                <ul class="tm-footer-contact">
                    @if ($address)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 1 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <span>{{ $address }}</span>
                        </li>
                    @endif
                    @if ($email)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </li>
                    @endif
                    @if ($phone)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h1.38a1.5 1.5 0 0 1 1.41 1l.81 2.86a1.5 1.5 0 0 1-.46 1.75l-.9.72a11 11 0 0 0 5.23 5.23l.72-.9a1.5 1.5 0 0 1 1.75-.46l2.86.81a1.5 1.5 0 0 1 1 1.41v1.48a1.5 1.5 0 0 1-1.5 1.5h-.38A13.42 13.42 0 0 1 4 5.98v-.48Z"/></svg>
                            <a href="tel:{{ $phone }}">{{ config('site.phone') }}</a>
                        </li>
                    @endif
                    @if ($whatsapp)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11.5a8.5 8.5 0 0 1-12.5 7.5L3 20l1.1-4.3A8.5 8.5 0 1 1 20 11.5Z"/></svg>
                            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp us</a>
                        </li>
                    @endif
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h12M12 6l6 6-6 6"/></svg>
                        <a href="{{ url('/contact-us#request') }}">Send us a trip request</a>
                    </li>
                </ul>
            </div>
        </div>

        @if ($partners = config('site.partners', []))
            <div class="tm-footer-partners">
                <h3 class="tm-footer-heading">Proudly Associated With</h3>
                <ul>
                    @foreach ($partners as $partner)
                        <li>
                            @if (! empty($partner['url']))
                                <a href="{{ $partner['url'] }}" target="_blank" rel="noopener" class="tm-footer-partner">
                                    <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }}" loading="lazy" decoding="async" draggable="false">
                                </a>
                            @else
                                <span class="tm-footer-partner">
                                    <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }}" loading="lazy" decoding="async" draggable="false">
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="tm-footer-bottom">
            <p>&copy; {{ now()->year }} <span class="notranslate" translate="no">Shanyangi Adventures</span>. All rights reserved.</p>
            <ul>
                <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('/terms') }}">Terms &amp; Conditions</a></li>
            </ul>
        </div>
    </div>
</footer>
