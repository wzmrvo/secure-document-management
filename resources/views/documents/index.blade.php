@extends('layouts.app')

@section('title', 'Documents')
@section('page_title', 'Documents')

@php
    // MOCK DATA: replace with controller data later.
    $documents = require resource_path('mock/documents.php');
    $categories = require resource_path('mock/categories.php');
    $classifications = ['Public', 'Internal', 'Confidential', 'Restricted'];
    $formatDate = fn ($date) => \Carbon\Carbon::parse($date)->format('M j, Y');
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Documents</h1>
            <p>Securely organize, manage, and share your institution's documents.</p>
        </div>
    </div>

    <div data-table data-page-size="7">
        <div class="toolbar">
            <div class="search" role="search">
                <x-icon name="search" />
                <label class="sr-only" for="documents-search">Search by document name or owner</label>
                <input id="documents-search" type="search" class="input" placeholder="Search by document name or owner..." data-table-search>
            </div>
            <select class="select" data-table-filter="category" aria-label="Filter by category">
                <option value="all">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                @endforeach
            </select>
            <x-button variant="primary" :href="route('documents.create')">Upload document</x-button>
        </div>

        <div class="tabs" role="tablist" aria-label="Filter by classification" data-table-tabs="classification">
            <button type="button" class="tab is-active" role="tab" aria-selected="true" data-value="all">All documents <span class="tab__count" data-count>{{ count($documents) }}</span></button>
            @foreach (['Confidential', 'Internal', 'Restricted'] as $level)
                <button type="button" class="tab" role="tab" aria-selected="false" data-value="{{ $level }}">{{ $level }}</button>
            @endforeach
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" data-sort="title">Document name</th>
                            <th scope="col" data-sort="category">Category</th>
                            <th scope="col" data-sort="uploader">Uploaded by</th>
                            <th scope="col" data-sort="date">Date uploaded</th>
                            <th scope="col" data-sort="classification">Classification</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-table-body>
                        @foreach ($documents as $doc)
                            <tr data-row data-id="{{ $doc['id'] }}" data-title="{{ $doc['file'] }}" data-category="{{ $doc['category'] }}"
                                data-uploader="{{ $doc['uploader'] }}" data-date="{{ $doc['date'] }}" data-classification="{{ $doc['classification'] }}">
                                <td>
                                    <div class="cell-doc">
                                        <span class="file-icon"><x-icon name="file" /></span>
                                        <a class="cell-doc__title js-title" href="{{ route('documents.show', $doc['id']) }}">{{ $doc['file'] }}</a>
                                    </div>
                                </td>
                                <td class="js-category">{{ $doc['category'] }}</td>
                                <td>{{ $doc['uploader'] }}</td>
                                <td>{{ $formatDate($doc['date']) }}</td>
                                <td><x-classification-badge :level="$doc['classification']" class="js-class" /></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('documents.show', $doc['id']) }}" aria-label="View {{ $doc['file'] }}" title="View"><x-icon name="eye" /></a>
                                        <button type="button" class="icon-btn" data-doc-action="edit" aria-label="Edit {{ $doc['file'] }}" title="Edit"><x-icon name="edit" /></button>
                                        <button type="button" class="icon-btn" data-doc-action="download" aria-label="Download {{ $doc['file'] }}" title="Download"><x-icon name="download" /></button>
                                        <button type="button" class="icon-btn icon-btn--danger" data-doc-action="delete" aria-label="Delete {{ $doc['file'] }}" title="Delete"><x-icon name="trash" /></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="empty-state" data-table-empty hidden>No documents match your search or filters.</div>
            </div>
            <div class="table-footer">
                <span data-table-count data-noun="documents"></span>
                <div class="pagination" data-pagination aria-label="Pagination"></div>
            </div>
        </div>
    </div>

    {{-- Edit document --}}
    <x-modal id="edit-doc-modal" title="Edit document">
        <form id="edit-doc-form" class="form-stack" method="post" novalidate>
            @csrf
            <div class="field">
                <label for="edit-doc-title">File name <span class="req">*</span></label>
                <input class="input" id="edit-doc-title" type="text" maxlength="120">
            </div>
            <div class="field">
                <label for="edit-doc-category">Category</label>
                <select class="select" id="edit-doc-category">
                    @foreach ($categories as $category)
                        <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="edit-doc-classification">Classification</label>
                <select class="select" id="edit-doc-classification">
                    @foreach ($classifications as $level)
                        <option value="{{ $level }}">{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="edit-doc-form">Save changes</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Delete document --}}
    <x-modal id="delete-doc-modal" title="Delete this document?">
        <p class="muted">"<strong id="delete-doc-name"></strong>" will be removed from the document list. This is a frontend demo, so nothing is deleted from a server.</p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="danger" id="confirm-delete-doc" data-modal-close>Delete</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/documents.js') }}"></script>
@endpush
