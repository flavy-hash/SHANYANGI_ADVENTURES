@extends('layouts.app')

@use('App\Support\ProtectedMedia')
@use('Illuminate\Support\Str')

@section('title', $stay->name.', '.$stay->location.' | Shanyangi Adventures')
@section('description', Str::limit(preg_replace('/\s+/', ' ', $stay->description), 155))

@section('content')
    @php($photos = $stay->photos)
    @php($details = array_filter(['Location' => $stay->location, 'Type' => $stay->type, 'Comfort level' => $stay->level]))

    <x-page-hero
        :title="$stay->name"
        :kicker="implode(' · ', array_filter([$stay->type, $stay->level]))"
        :subtitle="$stay->location"
        :image="$stay->image"
        :crumbs="[
            ['label' => 'Accommodation', 'url' => route('accommodations')],
            ['label' => $stay->location, 'url' => route('accommodations', ['location' => $stay->location_key])],
            ['label' => $stay->name],
        ]"
    />

    <section class="bg-tm-beige py-14 sm:py-20">
        <div class="tm-container">
            <div class="tm-package-layout">
                <div class="min-w-0">
                    <p class="tm-package-kicker">{{ $stay->location }}</p>
                    <h2 class="tm-package-heading">About {{ $stay->name }}</h2>
                    <div class="tm-package-overview">
                        @foreach ($stay->paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    @if (! empty($stay->facilities))
                        <h3 class="tm-package-subheading">Facilities</h3>
                        <ul class="tm-stay-facilities">
                            @foreach ($stay->facilities as $facility)
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg>
                                    {{ $facility }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($photos)
                        <h3 class="tm-package-subheading">Photo Gallery</h3>
                        <div class="tm-gallery" data-gallery>
                            @foreach ($photos as $i => $photo)
                                @if ($thumb = ProtectedMedia::src($photo, 800))
                                    <button type="button" @class(['tm-gallery-item', 'is-wide' => $loop->first && count($photos) > 2])
                                            data-gallery-item data-full="{{ ProtectedMedia::src($photo, 1600) }}"
                                            data-protected-media aria-label="Open photo {{ $i + 1 }} of {{ count($photos) }}">
                                        <img src="{{ $thumb }}" alt="{{ $stay->name }}, photo {{ $i + 1 }}" loading="lazy" decoding="async" draggable="false">
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="tm-booking-card" aria-label="Stay details">
                    <p class="tm-booking-from">Stay details</p>
                    <p class="tm-booking-price tm-booking-price-sm">{{ $stay->name }}</p>

                    @if ($details)
                        <dl class="tm-booking-facts">
                            @foreach ($details as $label => $value)
                                <div>
                                    <dt>{{ $label }}</dt>
                                    <dd>{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif

                    {{-- The stay's own website when set in the admin, otherwise our enquiry form. --}}
                    @if ($stay->website_url)
                        <a href="{{ $stay->website_url }}" class="tm-booking-primary" target="_blank" rel="noopener noreferrer nofollow">
                            Enquire About This Stay
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd"/></svg>
                        </a>
                    @else
                        <a href="{{ route('contact', ['trip' => 'Stay: '.$stay->name]) }}#request" class="tm-booking-primary">
                            Enquire About This Stay
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                        </a>
                    @endif
                    {{-- Came from an itinerary? Offer the way back to it. --}}
                    @php($fromTrip = str_starts_with(url()->previous(), route('safaris').'/'))
                    <a href="{{ $fromTrip ? url()->previous() : route('accommodations') }}" class="tm-booking-secondary">
                        {{ $fromTrip ? 'Back to the Itinerary' : 'All Stays' }}
                    </a>
                </aside>
            </div>
        </div>
    </section>

    {{-- Trips whose itinerary uses this stay --}}
    @if ($packages->isNotEmpty())
        <section class="bg-tm-cream py-14 sm:py-20" aria-labelledby="stay-trips-title">
            <div class="tm-container">
                <p class="tm-section-kicker">Stay Here On</p>
                <h2 id="stay-trips-title" class="tm-section-title">Trips That Include {{ $stay->name }}</h2>
                <div class="tm-divider" aria-hidden="true"></div>
                <div class="tm-card-grid mt-10">
                    @foreach ($packages as $package)
                        <x-package-card :package="$package" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($nearby->isNotEmpty())
        <section @class(['py-14 sm:py-20', 'bg-tm-beige' => $packages->isNotEmpty(), 'bg-tm-cream' => $packages->isEmpty()]) aria-labelledby="nearby-stays-title">
            <div class="tm-container">
                <p class="tm-section-kicker">Accommodation</p>
                <h2 id="nearby-stays-title" class="tm-section-title">More Stays in {{ $stay->location }}</h2>
                <div class="tm-divider" aria-hidden="true"></div>
                <div class="tm-card-grid mt-10">
                    @foreach ($nearby as $other)
                        <x-info-card
                            :title="$other->name"
                            :href="$other->url"
                            :image="$other->image"
                            :pill="$other->location"
                            :chips="array_values(array_filter([$other->type, $other->level]))"
                            :text="$other->description"
                            link-label="View stay"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Full-screen photo viewer for the gallery --}}
    @if ($photos)
        <dialog class="tm-lightbox" data-lightbox aria-label="{{ $stay->name }} photos">
            <figure class="tm-lightbox-figure" data-protected-media>
                <img src="" alt="" data-lightbox-image draggable="false">
                <figcaption class="tm-lightbox-count" data-lightbox-count></figcaption>
            </figure>
            <button type="button" class="tm-lightbox-btn is-close" data-lightbox-close aria-label="Close">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
            @if (count($photos) > 1)
                <button type="button" class="tm-lightbox-btn is-prev" data-lightbox-prev aria-label="Previous photo">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.83 10l3.94 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd"/></svg>
                </button>
                <button type="button" class="tm-lightbox-btn is-next" data-lightbox-next aria-label="Next photo">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.17 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
                </button>
            @endif
        </dialog>
    @endif

    <x-cta-band
        title="Want to stay at {{ $stay->name }}?"
        text="Tell us your dates and route, and we will build a trip around it."
    />
@endsection
