{{-- Live chat with the team (resources/js/chat.js, App\Http\Controllers\ChatController). --}}
<div class="tm-chat" data-chat
     data-start-url="{{ route('chat.start') }}"
     data-messages-url="{{ route('chat.messages') }}"
     data-send-url="{{ route('chat.send') }}"
     data-typing-url="{{ route('chat.typing') }}">

    <section id="tm-chat-panel" class="tm-chat-panel" role="dialog" aria-modal="false" aria-labelledby="tm-chat-title" hidden data-chat-panel>
        <header class="tm-chat-head">
            <span class="tm-chat-avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20c1.5-4 4.5-6 9-6s7.5 2 9 6"/><circle cx="12" cy="8" r="4"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h2 id="tm-chat-title" class="tm-chat-title">Chat with our team</h2>
                <p class="tm-chat-sub" aria-live="polite" data-chat-sub>We usually reply within minutes during office hours (EAT).</p>
            </div>
            <button type="button" class="tm-chat-close" aria-label="Close chat" data-chat-toggle>
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/></svg>
            </button>
        </header>

        {{-- Before the chat starts --}}
        <form class="tm-chat-start" data-chat-start novalidate>
            <p class="tm-chat-intro">Hi there! Ask us anything about safaris, Kilimanjaro or Zanzibar. Leave your email so we can still reach you if you have to go.</p>

            <div class="tm-newsletter-hp" aria-hidden="true">
                <label for="tm-chat-website">Website</label>
                <input type="text" id="tm-chat-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <label class="sr-only" for="tm-chat-name">Your name</label>
            <input id="tm-chat-name" name="name" type="text" class="tm-chat-input" placeholder="Your name" maxlength="120" autocomplete="name">

            <label class="sr-only" for="tm-chat-email">Email (optional)</label>
            <input id="tm-chat-email" name="email" type="email" class="tm-chat-input" placeholder="Email (optional)" maxlength="255" autocomplete="email">

            <label class="sr-only" for="tm-chat-first">Your message</label>
            <textarea id="tm-chat-first" name="message" class="tm-chat-input" rows="3" placeholder="How can we help?" maxlength="2000" required></textarea>

            <p class="tm-chat-error" role="alert" data-chat-error hidden></p>
            <button type="submit" class="tm-chat-submit">Start chat</button>
            <p class="tm-chat-note">Messages are stored so our team can answer you. We never share them.</p>
        </form>

        {{-- During the chat --}}
        <div class="tm-chat-body" data-chat-body hidden>
            <ol class="tm-chat-messages" aria-live="polite" data-chat-messages></ol>
            <form class="tm-chat-composer" data-chat-composer novalidate>
                <label class="sr-only" for="tm-chat-message">Message</label>
                <textarea id="tm-chat-message" name="message" class="tm-chat-input" rows="1" placeholder="Write a message..." maxlength="2000" required></textarea>
                <button type="submit" class="tm-chat-send" aria-label="Send">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3.1 2.3a.75.75 0 0 0-.98.93l1.88 5.65a.75.75 0 0 0 .62.5l6.13.62-6.13.62a.75.75 0 0 0-.62.5l-1.88 5.65a.75.75 0 0 0 .98.93l15-6.75a.75.75 0 0 0 0-1.36l-15-6.75Z"/></svg>
                </button>
            </form>
            <p class="tm-chat-error tm-chat-error-inline" role="alert" data-chat-send-error hidden></p>
        </div>
    </section>

    <button type="button" class="tm-chat-launcher" aria-controls="tm-chat-panel" aria-expanded="false" data-chat-toggle data-chat-launcher>
        <svg class="tm-chat-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8.5 8.5 0 0 1-12.4 7.55L3 21l1.45-5.6A8.5 8.5 0 1 1 21 12Z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"/></svg>
        <svg class="tm-chat-icon-close" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/></svg>
        <span class="tm-chat-launcher-label">Chat with us</span>
        <span class="tm-chat-badge" data-chat-badge hidden>1</span>
    </button>
</div>
