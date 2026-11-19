{{-- Usage: <x-badge type="success|warning|danger|info|neutral|gold">Active</x-badge> --}}
@props(['type' => 'neutral'])
<span {{ $attributes->merge(['class' => 'badge badge--' . $type]) }}>{{ $slot }}</span>
