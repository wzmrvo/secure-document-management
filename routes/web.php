<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DocuSecure web routes (FRONTEND PHASE)
|--------------------------------------------------------------------------
| These routes only return Blade views backed by static mock data in
| resources/mock/*.php. There is no controller, database or authentication
| logic yet. When the backend phase starts, replace each closure/view route with
| a controller method and keep the route names (the sidebar and pages use them).
*/

// SECURITY NOTE: none of these routes is protected yet. In the backend phase every route below
// the login page must sit inside Route::middleware(['auth', ...]) and be checked by Policies.
Route::redirect('/', '/login');
Route::view('/login', 'auth.login')->name('login');

Route::view('/dashboard', 'dashboard')->name('dashboard');

// Documents (upload must stay above the {id} route)
Route::view('/documents', 'documents.index')->name('documents.index');
Route::view('/documents/upload', 'documents.create')->name('documents.create');
Route::get('/documents/{id}', function (int $id) {
    $documents = require resource_path('mock/documents.php');
    $document = collect($documents)->firstWhere('id', $id);
    abort_if($document === null, 404);

    return view('documents.show', ['document' => $document]);
})->whereNumber('id')->name('documents.show');

Route::view('/access', 'access.index')->name('access.index');
Route::view('/categories', 'categories.index')->name('categories.index');
Route::view('/users', 'users.index')->name('users.index');
Route::view('/audit-logs', 'audit.index')->name('audit.index');
Route::view('/settings', 'settings.index')->name('settings.index');
