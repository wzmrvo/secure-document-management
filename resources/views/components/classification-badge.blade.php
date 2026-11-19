{{-- Usage: <x-classification-badge level="Confidential" /> — Public (gray), Internal (blue), Confidential (gold), Restricted (red) --}}
@props(['level'])
@php
    $tone = ['Public' => 'neutral', 'Internal' => 'info', 'Confidential' => 'gold', 'Restricted' => 'danger'][$level] ?? 'neutral';
@endphp
<x-badge :type="$tone" {{ $attributes }}>{{ $level }}</x-badge>
