@extends('layouts.app')

@section('title', 'Categories')
@section('page_title', 'Categories')

@php
    // MOCK DATA: replace with controller data later. Document counts come from the mock document list.
    $categories = require resource_path('mock/categories.php');
    $documents = require resource_path('mock/documents.php');
    $counts = array_count_values(array_column($documents, 'category'));
    $classifications = ['Public', 'Internal', 'Confidential', 'Restricted'];
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Categories</h1>
            <p>Organize documents into categories so the right people can find them.</p>
        </div>
    </div>

    <div data-table>
        <div class="toolbar">
            <div class="search" role="search">
                <x-icon name="search" />
                <label class="sr-only" for="categories-search">Search categories</label>
                <input id="categories-search" type="search" class="input" placeholder="Search categories..." data-table-search>
            </div>
            <x-button variant="primary" icon="plus" id="add-category-btn">Add category</x-button>
        </div>

        <div class="category-grid" data-table-body>
            @foreach ($categories as $category)
                @include('categories._card', ['category' => $category, 'count' => $counts[$category['name']] ?? 0])
            @endforeach
        </div>
        <div class="empty-state card" data-table-empty hidden>No categories match your search.</div>
        <p class="muted" style="margin-top: var(--space-4); font-size: var(--fs-sm);" data-table-count data-noun="categories"></p>
    </div>

    {{-- Card markup reused when a category is added --}}
    <template id="category-template">
        @include('categories._card', ['category' => ['name' => '', 'description' => '', 'classification' => 'Internal'], 'count' => 0])
    </template>

    {{-- Add / edit --}}
    <x-modal id="category-modal" title="Add category">
        <form id="category-form" class="form-stack" novalidate>
            <div class="field">
                <label for="category-name">Category name <span class="req">*</span></label>
                <input class="input" id="category-name" type="text" maxlength="60">
            </div>
            <div class="field">
                <label for="category-description">Description</label>
                <textarea class="textarea" id="category-description" maxlength="160" placeholder="What kind of documents belong here?"></textarea>
            </div>
            <div class="field">
                <label for="category-classification">Default classification</label>
                <select class="select" id="category-classification">
                    @foreach ($classifications as $level)
                        <option value="{{ $level }}" @selected($level === 'Internal')>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="category-form" id="category-save-btn">Save category</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Delete --}}
    <x-modal id="delete-category-modal" title="Delete this category?">
        <p class="muted" id="delete-category-text"></p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="danger" id="confirm-delete-category" data-modal-close>Delete</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/categories.js') }}"></script>
@endpush
