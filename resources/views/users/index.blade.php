@extends('layouts.app')

@section('title', 'User management')
@section('page_title', 'User management')

@php
    // MOCK DATA: replace with controller data later.
    $users = require resource_path('mock/users.php');
    $roles = [
        'Administrator'   => ['icon' => 'shield', 'description' => 'Full access to documents, users, and settings.'],
        'Staff'           => ['icon' => 'users',  'description' => 'Can upload, view, and download assigned documents.'],
        'Authorized user' => ['icon' => 'key',    'description' => 'Can view documents only after approval.'],
    ];
    $roleCounts = array_count_values(array_column($users, 'role'));
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>User management</h1>
            <p>Manage people, their roles, and account status.</p>
        </div>
    </div>

    <div class="role-grid" id="role-cards">
        @foreach ($roles as $role => $info)
            <button type="button" class="role-card" data-role-card="{{ $role }}" aria-pressed="false">
                <span class="role-card__title">
                    <x-icon :name="$info['icon']" /> {{ $role }}
                    <span class="role-card__count" data-role-count="{{ $role }}">{{ $roleCounts[$role] ?? 0 }}</span>
                </span>
                <span class="role-card__desc">{{ $info['description'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- data-current-user: the signed-in admin (mock). Used only to demonstrate "no self role change / no self deactivation". --}}
    <div data-table data-page-size="6" data-current-user="alex.morgan@cvsu.edu.ph">
        <div class="toolbar">
            <div class="search" role="search">
                <x-icon name="search" />
                <label class="sr-only" for="users-search">Search by name or email</label>
                <input id="users-search" type="search" class="input" placeholder="Search by name or email..." data-table-search>
            </div>
            <select class="select" data-table-filter="role" id="role-filter" aria-label="Filter by role">
                <option value="all">All roles</option>
                @foreach (array_keys($roles) as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            <x-button variant="primary" icon="user-plus" id="add-user-btn">Add user</x-button>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" data-sort="name">Name</th>
                            <th scope="col" data-sort="role">Assigned role</th>
                            <th scope="col" data-sort="status">Status</th>
                            <th scope="col" data-sort="date">Date registered</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-table-body>
                        @foreach ($users as $user)
                            @include('users._row', ['user' => $user])
                        @endforeach
                    </tbody>
                </table>
                <div class="empty-state" data-table-empty hidden>No users match your search or filters.</div>
            </div>
            <div class="table-footer">
                <span data-table-count data-noun="users"></span>
                <div class="pagination" data-pagination aria-label="Pagination"></div>
            </div>
        </div>
    </div>

    <template id="user-row-template">
        @include('users._row', ['user' => ['name' => '', 'email' => '', 'role' => 'Staff', 'status' => 'Active', 'date' => '']])
    </template>

    <x-modal id="deactivate-user-modal" title="Deactivate this user?">
        <p class="muted"><strong id="deactivate-user-name"></strong> will no longer be able to sign in. Their documents and history stay in the system. This action is recorded in the audit log once the backend exists.</p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="danger" id="confirm-deactivate-user" data-modal-close>Deactivate</x-button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="user-modal" title="Add user">
        <form id="user-form" class="form-stack" method="post" novalidate>
            @csrf
            <div class="field">
                <label for="user-name">Full name <span class="req">*</span></label>
                <input class="input" id="user-name" type="text" maxlength="80">
            </div>
            <div class="field">
                <label for="user-email">Email address <span class="req">*</span></label>
                <input class="input" id="user-email" type="email" maxlength="120" placeholder="name@cvsu.edu.ph">
            </div>
            <div class="form-grid-2">
                <div class="field">
                    <label for="user-role">Role</label>
                    <select class="select" id="user-role">
                        @foreach (array_keys($roles) as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="user-status">Status</label>
                    <select class="select" id="user-status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="user-form">Save user</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/users.js') }}"></script>
@endpush
