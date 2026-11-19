<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DocuSecure web routes (FRONTEND PHASE)
|--------------------------------------------------------------------------
| These routes only return Blade views. There is no controller, database or
| authentication logic yet. When the backend phase starts, replace each closure
| with a controller method and keep the route names (the sidebar uses them).
*/

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard')->name('dashboard');

// Modules scheduled for later phases use a shared placeholder so navigation works.
$pending = [
    ['/documents',        'documents.index',  'Documents',         'file',      'Phase 3'],
    ['/documents/upload', 'documents.create', 'Upload Document',   'upload',    'Phase 3'],
    ['/access',           'access.index',     'Access Requests',   'key',       'Phase 4'],
    ['/categories',       'categories.index', 'Categories',        'tag',       'Phase 5'],
    ['/users',            'users.index',      'User Management',   'users',     'Phase 5'],
    ['/audit-logs',       'audit.index',      'Audit Logs',        'clipboard', 'Phase 6'],
    ['/settings',         'settings.index',   'Settings',          'settings',  'Phase 6'],
];

foreach ($pending as [$uri, $name, $title, $icon, $phase]) {
    Route::view($uri, 'pages.placeholder', compact('title', 'icon', 'phase'))->name($name);
}
