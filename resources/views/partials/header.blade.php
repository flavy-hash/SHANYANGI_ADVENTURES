@php
    $navItems = config('navigation.primary', []);
    $languages = config('navigation.languages', []);

    $isActive = function (string $path): bool {
        $segment = trim(parse_url($path, PHP_URL_PATH) ?? '/', '/');

        return $segment === '' ? request()->is('/') : request()->is($segment, $segment.'/*');
    };

    $langLabel = fn (string $code) => strtoupper(strtok($code, '-'));
    $sourceLang = array_key_first($languages) ?? 'en';
@endphp

{{-- Google Translate mounts its (hidden) widget here; the language menus below drive it. --}}
<div id="google_translate_element" class="tm-google-translate-element" aria-hidden="true"
     data-google-translate data-source-lang="{{ $sourceLang }}" data-languages="{{ implode(',', array_keys($languages)) }}"></div>

<header class="tm-header fixed inset-x-0 top-0 z-40" data-site-header>
    <div class="tm-container">
        <div class="flex h-20 items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center" aria-label="Shanyangi Adventures home">
                @include('partials.logo')
            </a>

            <nav class="hidden self-stretch lg:flex" aria-label="Primary">
                <ul class="flex items-stretch gap-[clamp(.75rem,1.25vw,1.75rem)]" data-menu-group data-menu-hover>
                    @foreach ($navItems as $item)
                        <li class="flex items-center">
                            @if (! empty($item['children']))
                                <button type="button" data-menu-trigger aria-expanded="false" aria-controls="mega-{{ Str::slug($item['label']) }}"
                                        @class(['tm-nav-link tm-nav-trigger', 'tm-nav-active' => $isActive($item['path'])])>
                                    {{ $item['label'] }}
                                    <svg class="tm-nav-caret" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                @php
                                    // Panel previews: the item's own "mega" copy first (default), then one per
                                    // child that defines its own title/text; other children show the default.
                                    $megaId = 'mega-'.Str::slug($item['label']);
                                    $previews = [$megaId.'-default' => [
                                        'title' => $item['mega']['title'] ?? $item['label'],
                                        'text' => $item['mega']['text'] ?? null,
                                        'cta' => $item['mega']['cta'] ?? 'Explore '.$item['label'],
                                        'image' => $item['mega']['image'] ?? null,
                                        'path' => $item['path'],
                                    ]];
                                    $childTargets = [];
                                    foreach ($item['children'] as $i => $child) {
                                        $previewId = $megaId.'-'.Str::slug($child['label']);
                                        if (isset($child['title']) || isset($child['text'])) {
                                            $previews[$previewId] = [
                                                'title' => $child['title'] ?? $child['label'],
                                                'text' => $child['text'] ?? null,
                                                'cta' => $child['cta'] ?? $child['label'],
                                                'image' => $child['image'] ?? $previews[$megaId.'-default']['image'],
                                                'path' => $child['path'],
                                            ];
                                            $childTargets[$i] = $previewId;
                                        } else {
                                            $childTargets[$i] = $megaId.'-default';
                                        }
                                    }
                                @endphp

                                <div class="tm-mega" id="{{ $megaId }}" hidden data-mega-panel>
                                    <ul class="tm-mega-links">
                                        @foreach ($item['children'] as $i => $child)
                                            <li><a href="{{ url($child['path']) }}" data-mega-target="{{ $childTargets[$i] }}">{{ $child['label'] }}</a></li>
                                        @endforeach
                                    </ul>

                                    @foreach ($previews as $previewId => $preview)
                                        <div class="tm-mega-preview" id="{{ $previewId }}" data-mega-preview @unless ($loop->first) hidden @endunless>
                                            <div class="tm-mega-body">
                                                <h2 class="tm-mega-title">{{ $preview['title'] }}</h2>
                                                @if ($preview['text'])
                                                    <p class="tm-mega-text">{{ $preview['text'] }}</p>
                                                @endif
                                                <a class="tm-mega-cta" href="{{ url($preview['path']) }}">
                                                    {{ $preview['cta'] }}
                                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                                                    </svg>
                                                </a>
                                            </div>

                                            @if ($preview['image'])
                                                <img class="tm-mega-img" src="{{ $preview['image'] }}" alt="" loading="lazy" decoding="async" draggable="false">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ url($item['path']) }}"
                                   @class(['tm-nav-link', 'tm-nav-active' => $isActive($item['path'])])
                                   @if ($isActive($item['path'])) aria-current="page" @endif>
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <details class="notranslate relative" translate="no" data-dropdown>
                    <summary class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white/85 px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/15 transition hover:bg-white" aria-label="Language">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/>
                        </svg>
                        <span data-current-lang>{{ $langLabel($sourceLang) }}</span>
                        <svg class="tm-lang-caret h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/>
                        </svg>
                    </summary>
                    <div class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-slate-900/10">
                        <div class="grid gap-1 p-2">
                            @foreach ($languages as $code => $name)
                                <button type="button" class="tm-lang-option" data-lang="{{ $code }}" aria-pressed="{{ $code === $sourceLang ? 'true' : 'false' }}">
                                    <span class="w-6 text-xs font-bold text-tm-green">{{ $langLabel($code) }}</span>
                                    <span class="flex-1">{{ $name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </details>

                <a href="{{ url('/contact-us#request') }}" class="tm-btn-primary">Send a Request</a>
            </div>

            <button type="button" class="tm-mobile-toggle inline-flex h-11 w-11 items-center justify-center lg:hidden"
                    data-mobile-toggle aria-expanded="false" aria-controls="mobileMenu">
                <span class="sr-only">Open menu</span>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"/>
                </svg>
            </button>
        </div>
    </div>
</header>

{{-- Kept outside <header>: the header's backdrop-filter would otherwise trap this fixed overlay. --}}
<div id="mobileMenu" class="tm-mobile-panel lg:hidden" data-mobile-panel aria-hidden="true">
    <div class="tm-mobile-drawer" role="dialog" aria-modal="true" aria-label="Site menu">
        <div class="tm-mobile-drawer-head">
            <span class="notranslate" translate="no">Shanyangi Adventures</span>
            <button type="button" class="tm-mobile-drawer-close" data-mobile-close aria-label="Close menu">
                <svg class="h-[1.15rem] w-[1.15rem]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L10.94 12l-5.72 5.72a.75.75 0 1 0 1.06 1.06L12 13.06l5.72 5.72a.75.75 0 1 0 1.06-1.06L13.06 12l5.72-5.72a.75.75 0 0 0-1.06-1.06L12 10.94 6.28 5.22Z"/>
                </svg>
            </button>
        </div>

        <nav class="grid gap-1" aria-label="Mobile" data-menu-group>
            @foreach ($navItems as $item)
                @if (! empty($item['children']))
                    <button type="button" data-menu-trigger aria-expanded="false" aria-controls="mobile-sub-{{ Str::slug($item['label']) }}"
                            @class(['tm-mobile-link tm-mobile-trigger', 'tm-nav-active' => $isActive($item['path'])])>
                        {{ $item['label'] }}
                        <svg class="tm-nav-caret" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div class="tm-mobile-submenu" id="mobile-sub-{{ Str::slug($item['label']) }}" hidden>
                        @foreach ($item['children'] as $child)
                            <a href="{{ url($child['path']) }}">{{ $child['label'] }}</a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ url($item['path']) }}"
                       @class(['tm-mobile-link', 'tm-nav-active' => $isActive($item['path'])])
                       @if ($isActive($item['path'])) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="mt-4 grid gap-2">
            <details class="notranslate" translate="no" data-dropdown>
                <summary class="inline-flex w-full cursor-pointer items-center justify-between gap-3 rounded-xl bg-white px-3 py-3 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 hover:bg-slate-50" aria-label="Language">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/>
                        </svg>
                        <span data-current-lang>{{ $langLabel($sourceLang) }}</span>
                    </span>
                    <svg class="tm-lang-caret h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/>
                    </svg>
                </summary>
                <div class="mt-2 grid gap-1 rounded-2xl bg-white p-2 ring-1 ring-slate-900/10">
                    @foreach ($languages as $code => $name)
                        <button type="button" class="tm-lang-option" data-lang="{{ $code }}" aria-pressed="{{ $code === $sourceLang ? 'true' : 'false' }}">
                            <span class="w-6 text-xs font-bold text-tm-green">{{ $langLabel($code) }}</span>
                            <span class="flex-1">{{ $name }}</span>
                        </button>
                    @endforeach
                </div>
            </details>

            <a href="{{ url('/contact-us#request') }}" class="tm-btn-primary w-full">Send a Request</a>
        </div>

        <x-social-links class="tm-social-light tm-drawer-social" />
    </div>
</div>
