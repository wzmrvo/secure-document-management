@extends('layouts.app')

@php
    /** @var array $document  Provided by routes/web.php (mock data). */
    $categories = require resource_path('mock/categories.php');
    $classifications = ['Public', 'Internal', 'Confidential', 'Restricted'];
    $title = pathinfo($document['file'], PATHINFO_FILENAME);
    $extension = strtoupper(pathinfo($document['file'], PATHINFO_EXTENSION));
    $dateLabel = \Carbon\Carbon::parse($document['date'])->format('M j, Y');

    // Mock access rules per classification (shown under "Current access")
    $accessRules = [
        'Public'       => [['Administrators', 'Full access'], ['Staff', 'View and download'], ['Authorized users', 'View and download']],
        'Internal'     => [['Administrators', 'Full access'], ['Staff', 'View and download'], ['Authorized users', 'View only']],
        'Confidential' => [['Administrators', 'Full access'], ['Staff', 'View and download'], ['Authorized users', 'Approval required']],
        'Restricted'   => [['Administrators', 'Full access'], ['Staff', 'Approval required'], ['Authorized users', 'Approval required']],
    ];
@endphp

@section('title', $title)
@section('page_title', 'Document details')

@section('content')
    <ol class="breadcrumb breadcrumb--page" aria-label="Breadcrumb">
        <li><a href="{{ route('documents.index') }}">Documents</a></li>
        <li><span data-bind="category">{{ $document['category'] }}</span></li>
        <li aria-current="page">Document details</li>
    </ol>

    <div class="page-head">
        <div>
            <h1 data-bind="title">{{ $title }}</h1>
            <p><span data-bind="file">{{ $document['file'] }}</span> · {{ $extension }} document · {{ $document['size'] }}</p>
        </div>
        <x-button variant="secondary" icon="edit" data-modal-open="#edit-details-modal">Edit</x-button>
    </div>

    <div class="grid grid--details">
        {{-- Preview --}}
        <section class="card" aria-labelledby="preview-title">
            <div class="card__head">
                <h3 id="preview-title">Document preview</h3>
                <span class="muted" style="font-size: var(--fs-xs);">Page 1 of 24 · 100%</span>
            </div>
            <div class="card__body">
                <div class="preview" role="img" aria-label="Preview placeholder for {{ $title }}">
                    <div class="preview__page">
                        <div class="preview__org">Cavite State University</div>
                        <div class="preview__office">Preview placeholder</div>
                        <div class="preview__rule"></div>
                        <div class="preview__title" data-bind="title">{{ $title }}</div>
                        <div class="preview__year">2026 — 2027</div>
                        <div class="preview__tagline">Sample first page shown until real file rendering is connected.</div>
                        <div class="preview__foot"><span data-bind="classification">{{ $document['classification'] }}</span> · Internal distribution</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="stack">
            {{-- Information --}}
            <section class="card" aria-labelledby="info-title">
                <div class="card__head"><h3 id="info-title">Document information</h3></div>
                <div class="card__body">
                    <div style="margin-bottom: var(--space-4);">
                        <x-classification-badge :level="$document['classification']" id="class-badge" />
                    </div>
                    <dl class="info-list">
                        <div><dt>Uploaded by</dt><dd>{{ $document['uploader'] }}</dd></div>
                        <div><dt>Date uploaded</dt><dd>{{ $dateLabel }} · 09:42 AM</dd></div>
                        <div><dt>Category</dt><dd data-bind="category">{{ $document['category'] }}</dd></div>
                        <div><dt>Current version</dt><dd>v2.1 · Latest</dd></div>
                    </dl>
                </div>
            </section>

            {{-- Access --}}
            <section class="card" aria-labelledby="access-title">
                <div class="card__head"><h3 id="access-title">Current access</h3></div>
                <div class="card__body">
                    <ul class="access-list" id="access-list">
                        @foreach ($accessRules[$document['classification']] as [$who, $what])
                            <li>{{ $who }} · <span>{{ $what }}</span></li>
                        @endforeach
                    </ul>
                    <x-button variant="primary" icon="download" style="width: 100%;" id="download-btn">Download document</x-button>
                </div>
            </section>

            {{-- Versions --}}
            <section class="card" aria-labelledby="versions-title">
                <div class="card__head"><h3 id="versions-title">Version history</h3></div>
                <div class="card__body">
                    <ul class="version-list">
                        <li>v2.1 · Updated faculty policies<small>{{ $dateLabel }} · {{ $document['uploader'] }}</small></li>
                        <li>v2.0 · Annual revision<small>Oct 28, 2026 · {{ $document['uploader'] }}</small></li>
                    </ul>
                    <button type="button" class="text-link-btn" style="margin-top: var(--space-4);" data-modal-open="#request-access-modal">View as unauthorized user →</button>
                </div>
            </section>

            {{-- Activity --}}
            <section class="card" aria-labelledby="activity-title">
                <div class="card__head"><h3 id="activity-title">Activity history</h3></div>
                <ul class="activity-list">
                    <li class="activity-item"><div><div class="activity-item__text"><strong>{{ $document['uploader'] }}</strong> uploaded v2.1</div><div class="activity-item__time">{{ $dateLabel }}</div></div></li>
                    <li class="activity-item"><div><div class="activity-item__text"><strong>Alex Morgan</strong> viewed this document</div><div class="activity-item__time">{{ $dateLabel }}</div></div></li>
                    <li class="activity-item"><div><div class="activity-item__text"><strong>James Wilson</strong> downloaded this document</div><div class="activity-item__time">Nov 17, 2026</div></div></li>
                </ul>
            </section>
        </div>
    </div>

    {{-- Edit details --}}
    <x-modal id="edit-details-modal" title="Edit document">
        <form id="edit-details-form" class="form-stack" novalidate>
            <div class="field">
                <label for="edit-title">Title <span class="req">*</span></label>
                <input class="input" id="edit-title" type="text" maxlength="120" value="{{ $title }}">
            </div>
            <div class="field">
                <label for="edit-category">Category</label>
                <select class="select" id="edit-category">
                    @foreach ($categories as $category)
                        <option value="{{ $category['name'] }}" @selected($category['name'] === $document['category'])>{{ $category['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="edit-classification">Classification</label>
                <select class="select" id="edit-classification">
                    @foreach ($classifications as $level)
                        <option value="{{ $level }}" @selected($level === $document['classification'])>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="edit-details-form">Save changes</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Unauthorized-user view: request access --}}
    <x-modal id="request-access-modal" title="Request access">
        <form id="request-access-form" class="form-stack" novalidate>
            <div class="note note--info" style="margin-bottom: 0;">
                <x-icon name="lock" />
                <span>This is what a user without permission sees. They must request access before they can open "<span data-bind="file">{{ $document['file'] }}</span>".</span>
            </div>
            <div class="field">
                <label for="request-reason">Reason for request <span class="req">*</span></label>
                <textarea class="textarea" id="request-reason" maxlength="300" placeholder="Explain why you need access"></textarea>
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="request-access-form">Send request</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script>window.DocuSecureAccessRules = @json($accessRules);</script>
    <script src="{{ asset('js/pages/document-show.js') }}"></script>
@endpush
