@props(['label' => null])

@php($links = \App\Support\SocialLinks::active())

{{-- Official social accounts (admin → Site settings). Renders nothing until a link is set. --}}
@if ($links)
    <ul {{ $attributes->class('tm-social') }} aria-label="{{ $label ?? 'Follow Shanyangi Adventures' }}">
        @foreach ($links as $network => $link)
            <li>
                <a href="{{ $link['url'] }}" target="_blank" rel="noopener me" aria-label="{{ $link['label'] }}" title="{{ $link['label'] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $link['icon'] !!}</svg>
                </a>
            </li>
        @endforeach
    </ul>
@endif
