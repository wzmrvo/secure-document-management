@php
    // Single source of truth for sidebar navigation (matches the Figma "Workspace" / "Administration" groups).
    // Route names are defined in routes/web.php. Upload Document is a button on the Documents page, not a nav item.
    $navSections = [
        'Workspace' => [
            ['label' => 'Dashboard',  'route' => 'dashboard',        'icon' => 'dashboard', 'match' => 'dashboard'],
            ['label' => 'Documents',  'route' => 'documents.index',  'icon' => 'file',      'match' => 'documents.*'],
            ['label' => 'Categories', 'route' => 'categories.index', 'icon' => 'folder',    'match' => 'categories.*'],
        ],
        'Administration' => [
            ['label' => 'Access Requests',  'route' => 'access.index',   'icon' => 'key',       'match' => 'access.*'],
            ['label' => 'User Management',  'route' => 'users.index',    'icon' => 'users',     'match' => 'users.*'],
            ['label' => 'Audit Logs',       'route' => 'audit.index',    'icon' => 'clock',     'match' => 'audit.*'],
            ['label' => 'Settings',         'route' => 'settings.index', 'icon' => 'settings',  'match' => 'settings.*'],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar__brand">
        {{-- Shield mark follows the Figma brand. The official CvSU logo (<x-logo-slot>) is reserved for the login screen until supplied. --}}
        <x-icon name="shield-check" class="brand-mark" />
        <div class="sidebar__brand-text">
            <div class="sidebar__brand-name">DocuSecure</div>
        </div>
    </div>

    <nav class="sidebar__nav">
        @foreach ($navSections as $heading => $items)
            <div class="sidebar__section">{{ $heading }}</div>
            @foreach ($items as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ $active ? 'is-active' : '' }}"
                   title="{{ $item['label'] }}"
                   @if ($active) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" />
                    <span class="nav-item__label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endforeach

        <button type="button" class="nav-item nav-item--logout" data-modal-open="#logout-modal" title="Logout">
            <x-icon name="logout" />
            <span class="nav-item__label">Logout</span>
        </button>
    </nav>

    <div class="sidebar__footer">
        <div class="sidebar__status">
            <x-icon name="shield-check" />
            <div class="sidebar__status-text">
                <strong>Your workspace is secure</strong>
                <span>All systems operational</span>
            </div>
        </div>
    </div>
</aside>
