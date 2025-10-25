<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('index'))->name('index');

Route::get('/login', fn () => Inertia::render('auth/Login'))->name('login');
Route::post('/login/magic-link', [
    App\Http\Controllers\Auth\MagicLinkController::class,
    'sendMagicLink',
])->name('login.magic-link');
Route::get('/login/magic-link/verify/{user}', [
    App\Http\Controllers\Auth\MagicLinkController::class,
    'verify',
])
    ->name('login.magic-link.verify')
    ->middleware('signed');
Route::post('/login/password', [
    App\Http\Controllers\Auth\PasswordController::class,
    'authenticate',
])->name('login.password');
Route::post('/logout', [
    App\Http\Controllers\Auth\LogoutController::class,
    'logout',
])
    ->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/dashboard',
        fn () => Inertia::render('Dashboard', [
            'greeting' => session('greeting'),
        ]),
    )->name('dashboard');
});
