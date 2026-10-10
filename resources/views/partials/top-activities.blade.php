@use('App\Support\ProtectedMedia')

@php
    // image: full URL, or a file under storage/app/private/media/images (served via ProtectedMedia).
    $activities = [
        [
            'label' => '06:10 EAT',
            'title' => 'Game drives',
            'text' => 'Early-morning and late-afternoon drives through the Serengeti, Ngorongoro Crater and Tarangire, with guides who read the land and share the story behind every sighting.',
            'caption' => 'Seronera Plains · Golden Hour',
            'image' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1000&q=75',
        ],
        [
            'label' => '18:40 EAT',
            'title' => 'Sunset bush dinners',
            'text' => 'Toast the sunset over the savannah, then enjoy a freshly prepared meal under the acacias and dessert beneath more stars than you have ever seen.',
            'caption' => 'Sundowner · Ndutu Area',
            'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1000&q=75',
        ],
        [
            'label' => '05:30 EAT',
            'title' => 'Hot air balloon safaris',
            'text' => 'Float over the Serengeti at dawn and watch the plains wake up from above, then land for a celebratory bush breakfast.',
            'caption' => 'Balloon Launch · Seronera',
            'image' => 'https://images.unsplash.com/photo-1507608616759-54f48f0af0ee?auto=format&fit=crop&w=1000&q=75',
        ],
        [
            'label' => 'After the plains',
            'title' => 'Zanzibar: tropical wind-down',
            'text' => 'Trade dust for turquoise: dhow cruises, spice farm tours, Stone Town\'s alleys and sunset dining by the ocean to close your journey.',
            'caption' => 'Ocean Tour · Zanzibar',
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1000&q=75',
        ],
    ];

    $imageUrl = fn (?string $image) => str_starts_with((string) $image, 'http') ? $image : ProtectedMedia::imageUrl($image);
@endphp

<section id="activities" class="bg-tm-beige py-16 sm:py-24" aria-labelledby="activities-title">
    <div class="tm-container">
        <p class="tm-section-kicker tm-kicker-lined">Beyond the Game Drive</p>
        <h2 id="activities-title" class="tm-section-title">Top Activities on a Tanzania Safari</h2>
        <p class="tm-section-subtitle">
            A safari with <span class="notranslate" translate="no">Shanyangi Adventures</span> is more than game drives.
            It is an immersive celebration of Tanzania's wildlife, culture and golden light.
        </p>

        <div class="mx-auto mt-14 grid max-w-5xl gap-14 md:gap-16">
            @foreach ($activities as $activity)
                <article @class(['tm-activity', 'is-reversed' => $loop->even])>
                    <div class="tm-activity-copy">
                        <p class="tm-activity-label">{{ $activity['label'] }}</p>
                        <h3 class="tm-activity-title">{{ $activity['title'] }}</h3>
                        <p class="tm-activity-text">{{ $activity['text'] }}</p>
                    </div>

                    <figure class="tm-activity-media" data-protected-media>
                        @if ($src = $imageUrl($activity['image'] ?? null))
                            <img src="{{ $src }}" alt="" loading="lazy" decoding="async" draggable="false">
                        @endif
                        <figcaption class="tm-activity-caption">{{ $activity['caption'] }}</figcaption>
                    </figure>
                </article>
            @endforeach
        </div>

        <div class="mt-14 text-center">
            <a href="{{ url('/activities') }}" class="tm-text-link">
                Explore All Activities
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
</section>
