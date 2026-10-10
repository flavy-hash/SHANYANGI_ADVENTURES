@props(['rating' => 5])

@php($rating = max(0, min(5, (int) round($rating))))

<span class="tm-review-stars" role="img" aria-label="Rated {{ $rating }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <svg viewBox="0 0 20 20" @class(['is-empty' => $i > $rating]) aria-hidden="true">
            <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9l-5.2 2.7 1-5.8L1.5 7.7l5.9-.9L10 1.5Z"/>
        </svg>
    @endfor
</span>
