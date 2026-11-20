<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <meta name="robots" content="noindex, nofollow">
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

    {{-- Logout confirmation. Demo: just navigates to the login page. The real logout must be a POST
         (with CSRF) that invalidates the session on the server; a GET link cannot be trusted to log out. --}}
    <x-modal id="logout-modal" title="Log out of DocuSecure?">
        <p class="muted">You will need to sign in again to access institutional documents.</p>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" :href="route('login', ['reason' => 'loggedout'])">Log out</x-button>
        </x-slot:footer>
    </x-modal>

    <div class="toast-stack" id="toast-stack" aria-live="polite"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    <script src="{{ asset('js/data-table.js') }}"></script>
    @stack('scripts')
</body>
</html>
