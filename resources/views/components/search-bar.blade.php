{{-- Usage: <x-search-bar placeholder="Search documents..." target="#recent-uploads-body" /> filters rows with [data-filter-row] --}}
@props(['placeholder' => 'Search...', 'target' => null])
<div class="search" role="search">
    <x-icon name="search" />
    <label class="sr-only" for="search-{{ md5($placeholder) }}">{{ $placeholder }}</label>
    <input id="search-{{ md5($placeholder) }}" type="search" class="input" placeholder="{{ $placeholder }}"
           @if ($target) data-filter-input data-filter-target="{{ $target }}" @endif>
</div>
