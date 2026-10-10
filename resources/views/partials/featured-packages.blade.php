@php
    $packages = \App\Models\Package::query()->published()->where('is_featured', true)->ordered()->get();
@endphp

@if ($packages->isNotEmpty())
    <section id="packages" class="bg-tm-beige py-16 sm:py-24" aria-labelledby="packages-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Featured Packages</p>
            <h2 id="packages-title" class="tm-section-title">Popular Packages to Start Planning</h2>
            <div class="tm-divider" aria-hidden="true"></div>
            <p class="tm-section-subtitle">
                Ready-made journeys you can book as they are, or use as a starting point for your own trip.
            </p>

            <div class="tm-card-grid mt-12">
                @foreach ($packages as $package)
                    <x-package-card :package="$package" />
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('safaris') }}" class="tm-btn-outline-dark">View All Packages</a>
            </div>
        </div>
    </section>
@endif
