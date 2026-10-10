<section id="newsletter" class="tm-newsletter" aria-labelledby="newsletter-title">
    <div class="tm-newsletter-inner tm-container">
        <p class="tm-newsletter-kicker">Stay Updated</p>
        <h2 id="newsletter-title" class="tm-newsletter-title">New adventures, first</h2>
        <div class="tm-divider tm-newsletter-divider" aria-hidden="true"></div>
        <p class="tm-newsletter-text">
            Occasional notes on new routes, quiet-season openings and where the herds are heading. No noise.
        </p>

        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="tm-newsletter-form" data-newsletter-form novalidate>
            @csrf
            <input type="hidden" name="source" value="footer">

            {{-- Honeypot: hidden from people, filled by bots. --}}
            <div class="tm-newsletter-hp" aria-hidden="true">
                <label for="newsletter-website">Website</label>
                <input type="text" id="newsletter-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <label for="newsletter-email" class="sr-only">Email address</label>
            <input type="email" id="newsletter-email" name="email" class="tm-newsletter-input" placeholder="Enter your email"
                   autocomplete="email" required maxlength="255" value="{{ old('email') }}"
                   aria-describedby="newsletter-status">
            <button type="submit" class="tm-newsletter-button">Subscribe</button>
        </form>

        <p id="newsletter-status" @class([
                'tm-newsletter-status',
                'is-success' => session('newsletter_success'),
                'is-error' => session('newsletter_error'),
            ]) role="status" aria-live="polite" data-newsletter-status>{{ session('newsletter_success') ?? session('newsletter_error') }}</p>
    </div>
</section>
