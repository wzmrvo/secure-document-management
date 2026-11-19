@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')

@php
    // MOCK DATA: replace with the signed-in user's data later.
    $admin = ['name' => 'Alex Morgan', 'email' => 'alex.morgan@cvsu.edu.ph', 'department' => 'Information Technology', 'role' => 'Administrator'];

    $notificationSettings = [
        ['id' => 'notif-requests', 'title' => 'Access requests', 'desc' => 'Notify me when someone requests access.', 'on' => true],
        ['id' => 'notif-uploads',  'title' => 'Document updates', 'desc' => 'Notify me when a document is uploaded or changed.', 'on' => true],
        ['id' => 'notif-digest',   'title' => 'Weekly digest',    'desc' => 'Send a weekly summary by email.', 'on' => false],
    ];
    $securitySettings = [
        ['id' => 'sec-2fa',      'title' => 'Require two-step authentication', 'desc' => 'Ask for a verification code at sign-in.', 'on' => true],
        ['id' => 'sec-download', 'title' => 'Log every download',              'desc' => 'Record each download in the audit log.', 'on' => true],
    ];
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Settings</h1>
            <p>Personalize your account and strengthen your workspace security.</p>
        </div>
    </div>

    <div class="tabs" role="tablist" aria-label="Settings sections" data-tabs>
        <button type="button" class="tab" role="tab" data-tab="profile">Profile</button>
        <button type="button" class="tab" role="tab" data-tab="security">Security</button>
        <button type="button" class="tab" role="tab" data-tab="notifications">Notifications</button>
    </div>

    {{-- PROFILE --}}
    <div class="settings-grid" data-tab-panel="profile">
        <section class="card" aria-labelledby="profile-title">
            <div class="card__head"><h3 id="profile-title">Administrator profile</h3></div>
            <div class="card__body">
                <p class="muted" style="font-size: var(--fs-sm); margin-bottom: var(--space-4);">Keep your contact details up to date.</p>
                <form id="profile-form" class="form-stack" novalidate>
                    <div class="form-grid-2">
                        <div class="field">
                            <label for="profile-name">Full name <span class="req">*</span></label>
                            <input class="input" id="profile-name" type="text" value="{{ $admin['name'] }}">
                        </div>
                        <div class="field">
                            <label for="profile-email">Email address <span class="req">*</span></label>
                            <input class="input" id="profile-email" type="email" value="{{ $admin['email'] }}">
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="field">
                            <label for="profile-department">Department</label>
                            <input class="input" id="profile-department" type="text" value="{{ $admin['department'] }}">
                        </div>
                        <div class="field">
                            <label for="profile-role">Assigned role</label>
                            <input class="input" id="profile-role" type="text" value="{{ $admin['role'] }}" disabled>
                        </div>
                    </div>
                    <div><x-button variant="primary" type="submit">Save changes</x-button></div>
                </form>
            </div>
        </section>

        <section class="card" aria-labelledby="password-title">
            <div class="card__head"><h3 id="password-title">Password management</h3></div>
            <div class="card__body">
                <p class="muted" style="font-size: var(--fs-sm); margin-bottom: var(--space-4);">Use at least 8 characters with a mix of letters and numbers.</p>
                <form id="password-form" class="form-stack" novalidate>
                    @foreach ([['current-password', 'Current password'], ['new-password', 'New password'], ['confirm-password', 'Confirm new password']] as [$id, $label])
                        <div class="field">
                            <label for="{{ $id }}">{{ $label }} <span class="req">*</span></label>
                            <div class="input-group">
                                <input class="input" id="{{ $id }}" type="password" autocomplete="{{ $id === 'current-password' ? 'current-password' : 'new-password' }}">
                                <button type="button" class="icon-btn" data-toggle-password="#{{ $id }}" aria-label="Show password" aria-pressed="false"><x-icon name="eye" /></button>
                            </div>
                        </div>
                    @endforeach
                    <div><x-button variant="primary" type="submit">Update password</x-button></div>
                </form>
            </div>
        </section>
    </div>

    {{-- SECURITY --}}
    <div class="settings-grid" data-tab-panel="security" hidden>
        <section class="card" aria-labelledby="security-title">
            <div class="card__head"><h3 id="security-title">System security</h3></div>
            <div class="card__body">
                @foreach ($securitySettings as $setting)
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-row__title">{{ $setting['title'] }}</div>
                            <div class="toggle-row__desc">{{ $setting['desc'] }}</div>
                        </div>
                        <label class="toggle">
                            <span class="sr-only">{{ $setting['title'] }}</span>
                            <input type="checkbox" id="{{ $setting['id'] }}" data-setting-label="{{ $setting['title'] }}" @checked($setting['on'])>
                            <span class="toggle__track"></span>
                        </label>
                    </div>
                @endforeach
                <div class="toggle-row">
                    <div>
                        <div class="toggle-row__title">Session timeout</div>
                        <div class="toggle-row__desc">Sign out inactive users automatically.</div>
                    </div>
                    <div>
                        <label class="sr-only" for="session-timeout">Session timeout</label>
                        <select class="select" id="session-timeout" data-setting-label="Session timeout" style="height: 36px; width: 140px;">
                            <option>15 minutes</option>
                            <option selected>30 minutes</option>
                            <option>60 minutes</option>
                        </select>
                    </div>
                </div>
                <div class="note note--success" style="margin-top: var(--space-4);">
                    <strong>Encrypted and well configured</strong>
                    Your current settings meet the recommended security level.
                </div>
            </div>
        </section>
    </div>

    {{-- NOTIFICATIONS --}}
    <div class="settings-grid" data-tab-panel="notifications" hidden>
        <section class="card" aria-labelledby="notifications-title">
            <div class="card__head"><h3 id="notifications-title">Notification preferences</h3></div>
            <div class="card__body">
                @foreach ($notificationSettings as $setting)
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-row__title">{{ $setting['title'] }}</div>
                            <div class="toggle-row__desc">{{ $setting['desc'] }}</div>
                        </div>
                        <label class="toggle">
                            <span class="sr-only">{{ $setting['title'] }}</span>
                            <input type="checkbox" id="{{ $setting['id'] }}" data-setting-label="{{ $setting['title'] }}" @checked($setting['on'])>
                            <span class="toggle__track"></span>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/settings.js') }}"></script>
@endpush
