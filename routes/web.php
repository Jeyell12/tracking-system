<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

// Public vehicle routes
Route::get('/vehicles/{vehicle}', [\App\Http\Controllers\PublicVehicleController::class, 'show'])
    ->name('vehicles.public.show');
