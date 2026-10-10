@props([
    'title',
    'href',
    'image' => null,
    'pill' => null,        // badge on the photo, e.g. location or category
    'meta' => null,        // small line above the title, e.g. "12 Sep 2026 · 5 min read"
    'chips' => [],         // short facts shown as pills
    'text' => null,
    'linkLabel' => 'Learn more',
])

@use('App\Support\ProtectedMedia')

{{-- Generic card for activities, stays and blog posts. The title link covers the whole card. --}}
<article class="tm-info-card">
    <div class="tm-info-media">
        @if ($src = ProtectedMedia::src($image))
            <img src="{{ $src }}" alt="" loading="lazy" decoding="async" draggable="false">
        @endif
        @if ($pill)
            <span class="tm-package-category">{{ $pill }}</span>
        @endif
    </div>

    <div class="tm-info-body">
        @if ($meta)
            <p class="tm-info-meta">{{ $meta }}</p>
        @endif

        <h3 class="tm-info-title"><a href="{{ $href }}">{{ $title }}</a></h3>

        @if ($chips)
            <ul class="tm-package-chips">
                @foreach ($chips as $chip)
                    <li class="tm-chip">{{ $chip }}</li>
                @endforeach
            </ul>
        @endif

        @if ($text)
            <p class="tm-info-text">{{ $text }}</p>
        @endif

        <span class="tm-info-link" aria-hidden="true">
            {{ $linkLabel }}
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
        </span>
    </div>
</article>
