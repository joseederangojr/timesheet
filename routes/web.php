<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('index'))->name('index');

Route::get('/login', fn () => Inertia::render('auth/login'))->name('login');

Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::post('/magic-link', [
        App\Http\Controllers\Auth\MagicLinkController::class,
        'store',
    ])->name('magic-link.store');

    Route::get('/magic-link/{user}', [
        App\Http\Controllers\Auth\MagicLinkController::class,
        'show',
    ])
        ->name('magic-link.show')
        ->middleware('signed');

    Route::post('/password', [
        App\Http\Controllers\Auth\PasswordController::class,
        'store',
    ])->name('password.store');

    Route::delete('/session', [
        App\Http\Controllers\Auth\LogoutController::class,
        'destroy',
    ])
        ->name('session.destroy')
        ->middleware('auth');
});

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/dashboard',
        fn () => Inertia::render('dashboard', [
            'greeting' => session('greeting'),
        ]),
    )->name('dashboard');

});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get(
        '/dashboard',
        fn () => Inertia::render('admin/dashboard', [
            'greeting' => session('greeting'),
        ]),
    )->name('dashboard');

    Route::get('/users', [
        App\Http\Controllers\Admin\UsersController::class,
        'index',
    ])->name('users.index');
});
