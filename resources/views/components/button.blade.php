{{-- Usage: <x-button variant="primary|secondary|accent|danger" size="sm" href="..." icon="upload">Label</x-button> --}}
@props(['variant' => 'primary', 'size' => null, 'href' => null, 'icon' => null, 'type' => 'button'])
@php
    $classes = 'btn btn--' . $variant . ($size ? ' btn--' . $size : '');
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" />@endif
        {{ $slot }}
    </button>
@endif
