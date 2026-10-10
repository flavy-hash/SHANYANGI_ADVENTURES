@php
    // Latest three; the full list lives on the Reviews page.
    $all = \App\Support\Reviews::all();
    $reviews = $all->take(3);
@endphp

<section id="reviews" class="bg-tm-cream py-16 sm:py-24" aria-labelledby="reviews-title">
    <div class="tm-container">
        <p class="tm-section-kicker">Reviews</p>
        <h2 id="reviews-title" class="tm-section-title">What Travelers Are Saying</h2>
        <div class="tm-divider" aria-hidden="true"></div>

        @if ($reviews->isNotEmpty())
            <div class="tm-card-grid mt-12">
                @foreach ($reviews as $i => $review)
                    <x-review-card :review="$review" :id="'review-dialog-'.$i" />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('reviews') }}" class="tm-review-more">
                    {{ $all->count() > $reviews->count() ? 'Read All '.$all->count().' Reviews' : 'Read More Reviews' }}
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        @endif

        <x-review-badges />
    </div>
</section>
