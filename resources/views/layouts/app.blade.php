<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · DocuSecure</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/docusecure.css') }}">
    @stack('styles')
</head>
<body>
    @include('partials.icons')

    <div class="app">
        @include('partials.sidebar')
        <div class="sidebar-backdrop" aria-hidden="true"></div>

        <div class="app__main">
            @include('partials.header')

            <main class="app__content" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Logout confirmation (frontend demo only: the Login page is built in Phase 7) --}}
    <x-modal id="logout-modal" title="Log out of DocuSecure?">
        <p class="muted">You will need to sign in again to access institutional documents.</p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" data-modal-close data-toast="Logged out (demo). Login page arrives in Phase 7." data-toast-type="success">Log out</x-button>
        </x-slot:footer>
    </x-modal>

    <div class="toast-stack" id="toast-stack" aria-live="polite"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    <script src="{{ asset('js/data-table.js') }}"></script>
    @stack('scripts')
</body>
</html>
