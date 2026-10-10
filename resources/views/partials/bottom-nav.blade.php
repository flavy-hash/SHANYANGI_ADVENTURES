@php
    $whatsapp = preg_replace('/\D/', '', (string) config('site.whatsapp'));
    $phone = preg_replace('/[^\d+]/', '', (string) config('site.phone'));

    $whatsappUrl = $whatsapp
        ? 'https://wa.me/'.$whatsapp.'?text='.rawurlencode(config('site.whatsapp_message'))
        : url('/contact-us#request');
    $callUrl = $phone ? 'tel:'.$phone : url('/contact-us');

    $tabs = [
        ['label' => 'Home', 'href' => url('/'), 'active' => request()->is('/'), 'icon' => 'home'],
        ['label' => 'Safaris', 'href' => url('/safaris'), 'active' => request()->is('safaris', 'safaris/*'), 'icon' => 'compass'],
        ['label' => 'WhatsApp', 'href' => $whatsappUrl, 'feature' => true, 'external' => (bool) $whatsapp],
        ['label' => 'Call', 'href' => $callUrl, 'active' => ! $phone && request()->is('contact-us'), 'icon' => 'phone'],
    ];
@endphp

{{-- Mobile/tablet app-style tab bar. Hidden on desktop and while the drawer is open. --}}
<nav class="tm-bottom-nav lg:hidden" aria-label="Quick links">
    <div class="tm-bottom-nav-inner">
        @foreach ($tabs as $tab)
            @if (! empty($tab['feature']))
                <div class="tm-bottom-nav-feature">
                    <a href="{{ $tab['href'] }}" class="tm-bottom-nav-fab" aria-label="Chat with us on WhatsApp"
                       @if ($tab['external']) target="_blank" rel="noopener" @endif>
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.47-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.06 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.27-.2-.57-.35Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.49 2 2.01 6.49 2.01 12c0 1.76.46 3.42 1.27 4.85L2 22l5.29-1.25A9.96 9.96 0 0 0 12 21.94c5.51 0 10-4.48 10-9.99a9.93 9.93 0 0 0-2.93-7.07A9.93 9.93 0 0 0 12 2Zm5.89 15.88A8.28 8.28 0 0 1 12 20.32a8.29 8.29 0 0 1-4.23-1.16l-.3-.18-3.14.74.75-3.06-.2-.31A8.3 8.3 0 0 1 3.6 12c0-4.59 3.74-8.32 8.33-8.32a8.27 8.27 0 0 1 5.88 2.44 8.26 8.26 0 0 1 2.44 5.88 8.29 8.29 0 0 1-2.36 5.98Z"/>
                        </svg>
                    </a>
                    <span class="tm-bottom-nav-feature-label" aria-hidden="true">{{ $tab['label'] }}</span>
                </div>
            @else
                <a href="{{ $tab['href'] }}" @class(['tm-bottom-nav-item', 'is-active' => $tab['active']])
                   @if ($tab['active']) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @switch($tab['icon'])
                            @case('home')
                                <path d="M4 10.5 12 4l8 6.5"/><path d="M6 9v9.5a1 1 0 0 0 1 1h3v-5h4v5h3a1 1 0 0 0 1-1V9"/>
                                @break
                            @case('compass')
                                <circle cx="12" cy="12" r="8.25"/><path d="m14.5 9.5-1.5 5-5 1.5 1.5-5 5-1.5Z"/>
                                @break
                            @case('phone')
                                <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h1.38a1.5 1.5 0 0 1 1.41 1l.81 2.86a1.5 1.5 0 0 1-.46 1.75l-.9.72a11 11 0 0 0 5.23 5.23l.72-.9a1.5 1.5 0 0 1 1.75-.46l2.86.81a1.5 1.5 0 0 1 1 1.41v1.48a1.5 1.5 0 0 1-1.5 1.5h-.38A13.42 13.42 0 0 1 4 5.98v-.48Z"/>
                                @break
                        @endswitch
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endif
        @endforeach

        <button type="button" class="tm-bottom-nav-item" data-mobile-toggle-alias aria-controls="mobileMenu" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Menu
        </button>
    </div>
</nav>
