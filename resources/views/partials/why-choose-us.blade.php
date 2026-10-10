@php
    // Icons are 24x24 stroke paths (rendered with currentColor).
    $reasons = [
        [
            'title' => 'Local Experts',
            'text' => 'Tanzanian guides and planners who know the parks, seasons and wildlife movements first-hand.',
            'icon' => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 1 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/>',
        ],
        [
            'title' => 'Tailor-Made Trips',
            'text' => 'Every itinerary is private and built around your dates, interests, pace and budget.',
            'icon' => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
        ],
        [
            'title' => 'Safety First',
            'text' => 'Well-maintained 4x4 safari vehicles, careful route planning and support throughout your journey.',
            'icon' => '<path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
        ],
        [
            'title' => 'Clear, Fair Pricing',
            'text' => 'Transparent quotes that show exactly what is included, so there are no surprises later.',
            'icon' => '<path d="M3 12V5a2 2 0 0 1 2-2h7l9 9-9 9-9-9Z"/><circle cx="8" cy="8" r="1.5"/>',
        ],
        [
            'title' => 'Responsible Travel',
            'text' => 'We work with local communities, lodges and crews, so your trip supports the people who make it possible.',
            'icon' => '<path d="M5 19c0-8 5-13 14-14-1 9-6 14-14 14Z"/><path d="M5 19c3-3 6-5 9-7"/>',
        ],
        [
            'title' => 'Support on the Ground',
            'text' => 'From airport pickup to departure, our team is one call or WhatsApp message away.',
            'icon' => '<path d="M4 13v-1a8 8 0 0 1 16 0v1"/><rect x="3" y="13" width="4" height="6" rx="1.5"/><rect x="17" y="13" width="4" height="6" rx="1.5"/><path d="M20 19c0 1.5-2 2.5-5 2.5"/>',
        ],
    ];
@endphp

<section id="why-choose-us" class="bg-white py-16 sm:py-24" aria-labelledby="why-choose-us-title">
    <div class="tm-container">
        <p class="tm-section-kicker">Why Choose Us</p>
        <h2 id="why-choose-us-title" class="tm-section-title">Travel With People Who Know Tanzania</h2>
        <div class="tm-divider" aria-hidden="true"></div>
        <p class="tm-section-subtitle">
            Planning a safari is a big decision. Here is what you can count on when you travel with us.
        </p>

        <ul class="mx-auto mt-12 grid max-w-6xl gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($reasons as $reason)
                <li class="tm-feature-card">
                    <span class="tm-feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $reason['icon'] !!}</svg>
                    </span>
                    <h3 class="tm-feature-title">{{ $reason['title'] }}</h3>
                    <p class="tm-feature-text">{{ $reason['text'] }}</p>
                </li>
            @endforeach
        </ul>

        <div class="mt-12 text-center">
            <a href="{{ url('/contact-us#request') }}" class="tm-btn-primary">Start Planning Your Trip</a>
        </div>
    </div>
</section>
