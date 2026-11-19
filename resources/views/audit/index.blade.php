@extends('layouts.app')

@section('title', 'Audit logs')
@section('page_title', 'Audit logs')

@php
    // MOCK DATA: replace with controller data later.
    $logs = require resource_path('mock/audit-logs.php');
    $actions = collect($logs)->pluck('action')->unique()->sort()->values();
    $formatDateTime = fn ($value) => \Carbon\Carbon::parse($value)->format('M j, Y · H:i');
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Audit logs</h1>
            <p>A complete, tamper-aware record of every action in your workspace.</p>
        </div>
    </div>

    <div class="note note--info">
        <x-icon name="info" />
        <span>Logs are retained for 365 days. Entries cannot be edited or deleted from this screen.</span>
    </div>

    {{-- data-today is fixed so the mock date ranges behave the same on any day --}}
    <div data-table data-page-size="8" data-today="2026-11-19">
        <div class="toolbar">
            <div class="search" role="search">
                <x-icon name="search" />
                <label class="sr-only" for="audit-search">Search activity, people, or documents</label>
                <input id="audit-search" type="search" class="input" placeholder="Search activity, people, or documents..." data-table-search>
            </div>
            <select class="select" data-table-filter="action" aria-label="Filter by action">
                <option value="all">All actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}">{{ $action }}</option>
                @endforeach
            </select>
            <select class="select" data-table-filter="range" aria-label="Filter by date range">
                <option value="all">All time</option>
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
            <x-button variant="primary" icon="download" data-toast="Audit log export started (demo: no file is generated yet).">Export logs</x-button>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" data-sort="user">User</th>
                            <th scope="col" data-sort="action">Action performed</th>
                            <th scope="col" data-sort="document">Affected document</th>
                            <th scope="col" data-sort="date">Date and time</th>
                            <th scope="col">IP address</th>
                        </tr>
                    </thead>
                    <tbody data-table-body>
                        @foreach ($logs as $log)
                            <tr data-row data-user="{{ $log['user'] }}" data-action="{{ $log['action'] }}" data-document="{{ $log['document'] }}" data-date="{{ $log['datetime'] }}">
                                <td><span class="cell-doc__title">{{ $log['user'] }}</span></td>
                                <td class="link-text">{{ $log['action'] }}</td>
                                <td>{{ $log['document'] }}</td>
                                <td>{{ $formatDateTime($log['datetime']) }}</td>
                                <td class="muted">{{ $log['ip'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="empty-state" data-table-empty hidden>No activity matches your search or filters.</div>
            </div>
            <div class="table-footer">
                <span data-table-count data-noun="entries"></span>
                <div class="pagination" data-pagination aria-label="Pagination"></div>
            </div>
        </div>
    </div>
@endsection
