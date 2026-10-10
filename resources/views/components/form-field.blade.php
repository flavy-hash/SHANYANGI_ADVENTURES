@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'bag' => null,      // MessageBag holding this form's errors
])

@php
    $id = 'field-'.$name;
    $error = $bag?->first($name);
    $value = $value ?? old($name);
@endphp

<div class="tm-field">
    <label for="{{ $id }}" class="tm-label">
        {{ $label }}@if ($required) <span class="tm-required" aria-hidden="true">*</span>@endif
    </label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        @if ($required) required aria-required="true" @endif
        @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->class(['tm-input', 'is-invalid' => (bool) $error]) }}
    >
    @if ($error)
        <p id="{{ $id }}-error" class="tm-field-error">{{ $error }}</p>
    @endif
</div>
