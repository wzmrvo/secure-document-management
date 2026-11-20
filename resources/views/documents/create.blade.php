@extends('layouts.app')

@section('title', 'Upload document')
@section('page_title', 'Upload document')

@php
    // MOCK DATA: replace with controller data later.
    $categories = require resource_path('mock/categories.php');
    $classifications = ['Public', 'Internal', 'Confidential', 'Restricted'];
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Upload document</h1>
            <p>Add a document and choose how it's protected.</p>
        </div>
    </div>

    <form id="upload-form" method="post" novalidate
        @csrf data-redirect="{{ route('documents.index') }}">
        <div class="grid grid--2-1">
            <section class="card" aria-labelledby="doc-info-title">
                <div class="card__head"><h3 id="doc-info-title">Document information</h3></div>
                <div class="card__body form-stack">
                    <div class="field">
                        <label for="doc-title">Document title <span class="req">*</span></label>
                        <input class="input" id="doc-title" name="title" type="text" maxlength="120" placeholder="e.g. Faculty handbook 2026">
                    </div>
                    <div class="field">
                        <label for="doc-description">Description</label>
                        <textarea class="textarea" id="doc-description" name="description" maxlength="300" placeholder="Short summary of what this document contains"></textarea>
                        <span class="field__hint"><span id="description-count">0</span>/300 characters</span>
                    </div>
                    <div class="field">
                        <label for="doc-category">Category <span class="req">*</span></label>
                        <select class="select" id="doc-category" name="category">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset class="field" style="border: 0; padding: 0; margin: 0;">
                        <legend style="font-size: var(--fs-sm); font-weight: 600; padding: 0; margin-bottom: 6px;">Document classification</legend>
                        <div class="segmented" id="classification-group">
                            @foreach ($classifications as $level)
                                <input type="radio" name="classification" id="class-{{ strtolower($level) }}" value="{{ $level }}" @checked($level === 'Confidential')>
                                <label for="class-{{ strtolower($level) }}">{{ $level }}</label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="field">
                        <label id="file-label">File <span class="req">*</span></label>
                        <div class="dropzone" id="dropzone" role="button" tabindex="0" aria-labelledby="file-label">
                            <x-icon name="upload" />
                            <div class="dropzone__title">Drag and drop your file here</div>
                            <div>or <span class="link-text">browse files</span></div>
                            <div class="dropzone__hint">PDF, DOCX, XLSX, PPTX · Maximum 25 MB</div>
                        </div>
                        <input type="file" id="file-input" class="sr-only" accept=".pdf,.docx,.xlsx,.pptx" tabindex="-1">
                        <div class="file-row" id="file-row" hidden>
                            <x-icon name="file" />
                            <span class="file-row__name" id="file-name"></span>
                            <span class="muted" id="file-size"></span>
                            <x-icon name="check" class="file-row__ok" />
                            <button type="button" class="icon-btn" id="file-remove" aria-label="Remove file" style="width:28px;height:28px;"><x-icon name="x" /></button>
                        </div>
                        <div class="field__error" id="file-error" role="alert" hidden></div>
                    </div>

                    <div class="row">
                        <x-button variant="primary" type="submit">Submit document</x-button>
                        <x-button variant="secondary" :href="route('documents.index')">Cancel</x-button>
                    </div>
                </div>
            </section>

            <aside class="stack">
                <section class="card" aria-labelledby="perm-title">
                    <div class="card__head"><h3 id="perm-title">Access permissions</h3></div>
                    <div class="card__body">
                        <p class="muted" style="font-size: var(--fs-sm); margin-bottom: var(--space-4);">Choose who can access this document.</p>
                        <ul class="perm-list">
                            <li class="perm-item">
                                <div class="perm-item__title"><x-icon name="check" /> Administrators</div>
                                <p class="perm-item__desc">Full access · Always enabled</p>
                            </li>
                            <li class="perm-item">
                                <div class="perm-item__title"><x-icon name="check" /> Staff</div>
                                <label class="sr-only" for="perm-staff">Staff permission</label>
                                <select class="select" id="perm-staff" name="staff_permission">
                                    <option value="none">No access</option>
                                    <option value="view">View only</option>
                                    <option value="download" selected>View and download</option>
                                </select>
                            </li>
                            <li class="perm-item">
                                <div class="perm-item__title"><x-icon name="check" /> Authorized users</div>
                                <label class="sr-only" for="perm-authorized">Authorized users permission</label>
                                <select class="select" id="perm-authorized" name="authorized_permission">
                                    <option value="none">No access</option>
                                    <option value="view">View only</option>
                                    <option value="approval" selected>Request approval first</option>
                                </select>
                            </li>
                        </ul>
                        <div class="note" style="margin-top: var(--space-4);" id="classification-note">
                            <strong id="note-title">Confidential by default</strong>
                            <span id="note-text">Access is logged. Only permitted users can preview or download this file.</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/upload.js') }}"></script>
@endpush
