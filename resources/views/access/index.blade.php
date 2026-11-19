@extends('layouts.app')

@section('title', 'Access requests')
@section('page_title', 'Access requests')

@php
    // MOCK DATA: replace with controller data later.
    $requests = require resource_path('mock/access-requests.php');
    $formatDate = fn ($date) => \Carbon\Carbon::parse($date)->format('M j, Y');
    $statusLabels = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
    $statusTones = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Access requests</h1>
            <p>Review and act on requests to open restricted information and documents.</p>
        </div>
    </div>

    <div data-table>
        <div class="tabs tabs--pills" role="tablist" aria-label="Filter by status" data-table-tabs="status">
            @foreach ($statusLabels as $value => $label)
                <button type="button" class="tab {{ $value === 'pending' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $value === 'pending' ? 'true' : 'false' }}" data-value="{{ $value }}">
                    {{ $label }} <span class="tab__count" data-count>0</span>
                </button>
            @endforeach
        </div>

        <div class="note note--info">
            <x-icon name="info" />
            <span>Every decision is recorded in the audit log and the requester is notified.</span>
        </div>

        <div class="stack" data-table-body>
            @foreach ($requests as $request)
                <article class="card request-card" data-row data-status="{{ $request['status'] }}"
                         data-user="{{ $request['user'] }}" data-document="{{ $request['document'] }}" data-permission="{{ $request['permission'] }}">
                    <div class="request-card__user">
                        <span class="avatar avatar--blue" aria-hidden="true"><x-icon name="user" /></span>
                        <div>
                            <div class="cell-doc__title">{{ $request['user'] }}</div>
                            <div class="cell-doc__sub">{{ $request['email'] }}</div>
                        </div>
                    </div>
                    <div class="request-card__doc">
                        <div class="cell-doc__title">{{ $request['document'] }}</div>
                        <div class="cell-doc__sub">Requested {{ $formatDate($request['date']) }} · {{ $request['permission'] }}</div>
                    </div>
                    <div class="request-card__actions js-actions">
                        <x-badge :type="$statusTones[$request['status']]" class="js-status-badge">{{ $statusLabels[$request['status']] }}</x-badge>
                        @if ($request['status'] === 'pending')
                            <x-button variant="primary" size="sm" data-access-action="approve">Approve</x-button>
                            <x-button variant="secondary" size="sm" data-access-action="reject">Reject</x-button>
                        @endif
                    </div>
                    <p class="request-card__reason">"{{ $request['reason'] }}"</p>
                </article>
            @endforeach
        </div>
        <div class="empty-state card" data-table-empty hidden>No requests in this tab.</div>
    </div>

    <x-modal id="access-confirm-modal" title="Confirm this action">
        <p id="access-confirm-text" class="muted"></p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" id="access-confirm-btn" data-modal-close>Confirm</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/access.js') }}"></script>
@endpush
