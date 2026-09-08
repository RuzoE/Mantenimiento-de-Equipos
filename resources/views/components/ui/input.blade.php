@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
])

@php
    $field = $errors->has($name);
    $control = 'block w-full rounded-lg border-gray-300 shadow-sm sm:text-sm '
        . 'focus:border-brand-500 focus:ring-brand-500 '
        . 'disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 '
        . ($field ? 'border-red-400 text-red-900 focus:border-red-500 focus:ring-red-500' : '');
@endphp

<div>
    @if ($label)
        <x-ui.label :for="$name" :required="$required" class="mb-1">{{ $label }}</x-ui.label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($required) required @endif
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => $control]) }}
    >

    @if ($hint && ! $field)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
