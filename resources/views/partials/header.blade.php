@php
    // Mock notifications. Replace with real data when the Laravel backend is connected.
    $notifications = [
        ['text' => 'Dr. Emily Davis requested access to "Faculty handbook 2026"', 'time' => '10 minutes ago', 'unread' => true],
        ['text' => 'Sarah Chen uploaded "Q4 financial report"', 'time' => '1 hour ago', 'unread' => true],
        ['text' => 'Audit log export completed', 'time' => 'Yesterday', 'unread' => false],
    ];
    $hasUnread = collect($notifications)->contains('unread', true);
@endphp

<header class="header">
    {{-- Shown on tablet and mobile only (see .menu-toggle in CSS); on desktop the sidebar stays open like the Figma layout --}}
    <button type="button" class="icon-btn menu-toggle" data-sidebar-toggle aria-label="Toggle sidebar" aria-controls="sidebar" aria-expanded="true">
        <x-icon name="menu" />
    </button>

    <div class="header__title">@yield('page_title', 'Dashboard')</div>

    <div class="header__spacer"></div>

    {{-- Global search: submits to the Documents page (?q=). Documents filtering is built in Phase 3. --}}
    <form class="search header__search" role="search" method="GET" action="{{ route('documents.index') }}">
        <x-icon name="search" />
        <label class="sr-only" for="global-search">Search documents, people</label>
        <input id="global-search" type="search" name="q" class="input" placeholder="Search documents, people...">
    </form>

    {{-- Notifications --}}
    <div class="dropdown">
        <button type="button" class="icon-btn" data-dropdown-trigger aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
            <x-icon name="bell" />
            @if ($hasUnread)<span class="icon-btn__dot"></span>@endif
        </button>
        <div class="dropdown__menu notif-list">
            <div class="dropdown__head row">
                <strong>Notifications</strong>
                <button type="button" class="btn btn--sm btn--secondary" data-notif-mark-read>Mark all read</button>
            </div>
            @foreach ($notifications as $notification)
                <div class="notif-item {{ $notification['unread'] ? 'is-unread' : '' }}">
                    <div>
                        {{ $notification['text'] }}
                        <small>{{ $notification['time'] }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Profile menu --}}
    <div class="dropdown">
        <button type="button" class="profile-trigger" data-dropdown-trigger aria-expanded="false" aria-haspopup="true">
            <x-icon name="user" class="profile-trigger__icon" />
            <span>
                <div class="profile-trigger__name">Alex Morgan</div>
                <div class="profile-trigger__role">Administrator</div>
            </span>
            <x-icon name="chevron-down" class="icon" />
        </button>
        <div class="dropdown__menu">
            <div class="dropdown__head">
                <strong>Alex Morgan</strong>
                <span>alex.morgan@cvsu.edu.ph</span>
            </div>
            <a class="dropdown__item" href="{{ route('settings.index') }}"><x-icon name="settings" /> Account settings</a>
            <button type="button" class="dropdown__item dropdown__item--danger" data-modal-open="#logout-modal">
                <x-icon name="logout" /> Logout
            </button>
        </div>
    </div>
</header>
