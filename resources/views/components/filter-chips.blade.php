@props(['filters' => [], 'label' => 'Filter'])

{{-- Horizontal filter pills (scrolls sideways on small screens). --}}
<nav class="tm-filters" aria-label="{{ $label }}">
    <ul>
        @foreach ($filters as $filter)
            <li>
                <a href="{{ $filter['url'] }}" @class(['tm-filter', 'is-active' => $filter['active']])
                   @if ($filter['active']) aria-current="page" @endif>{{ $filter['label'] }}</a>
            </li>
        @endforeach
    </ul>
</nav>
