@php
    /** @var array{planned: int, added: int, left: int, percent: int} $completion */
    $done = $completion['planned'] > 0 && $completion['left'] === 0;
    $color = $done ? 'var(--success-500)' : ($completion['percent'] >= 50 ? 'var(--warning-500)' : 'var(--danger-500)');
@endphp

{{-- Itinerary progress: days written out of the trip's number of days. --}}
<div style="border: 1px solid var(--gray-200); border-radius: 0.75rem; padding: 0.9rem 1rem;" class="dark:border-white/10" data-package-progress>
    @if ($completion['planned'] === 0)
        <p style="font-size: 0.875rem; color: var(--gray-500);">
            Set the <strong>Number of days</strong> on the "Card &amp; banner" tab to track how much of the itinerary is still missing.
        </p>
    @else
        <div style="display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 0.25rem 1rem; font-size: 0.875rem;">
            <span style="font-weight: 600;">
                {{ $completion['added'] }} of {{ $completion['planned'] }} {{ $completion['planned'] === 1 ? 'day' : 'days' }} added
            </span>
            <span style="font-weight: 700; color: {{ $color }};">
                @if ($done)
                    100% complete
                @else
                    {{ $completion['percent'] }}% complete · {{ $completion['left'] }} {{ $completion['left'] === 1 ? 'day' : 'days' }} left
                @endif
            </span>
        </div>
        <div style="margin-top: 0.6rem; height: 0.5rem; overflow: hidden; border-radius: 9999px; background: var(--gray-200);"
             role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $completion['percent'] }}" aria-label="Itinerary progress">
            <div style="height: 100%; width: {{ $completion['percent'] }}%; border-radius: 9999px; background: {{ $color }}; transition: width 0.3s;"></div>
        </div>
    @endif
</div>
