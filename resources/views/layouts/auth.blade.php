<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Sign in') · DocuSecure</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/docusecure.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @stack('styles')
</head>
<body class="auth-body">
    @include('partials.icons')

    @yield('content')

    <div class="toast-stack" id="toast-stack" aria-live="polite"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    @stack('scripts')
</body>
</html>
