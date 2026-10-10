@props([
    'title' => 'Ready to start planning?',
    'text' => 'Tell us your dates, interests and budget, and we will put together a trip that fits you.',
])

@php($whatsapp = preg_replace('/\D/', '', (string) config('site.whatsapp')))

<section class="bg-tm-beige pb-16 sm:pb-24" aria-label="Plan your trip">
    <div class="tm-container">
        <div class="tm-cta-band">
            <div>
                <h2 class="tm-cta-band-title">{{ $title }}</h2>
                <p class="tm-cta-band-text">{{ $text }}</p>
            </div>
            <div class="tm-cta-band-actions">
                <a href="{{ route('contact') }}#request" class="tm-cta-band-primary">Send a Request</a>
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="tm-cta-band-secondary">Chat on WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
