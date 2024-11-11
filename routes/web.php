<?php

use App\Filament\Resources\OrderResource\Pages\OrderApiManagementPage;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/api/v1/orders-by-facility/{facility}/{year}/{month}', [OrderApiManagementPage::class, 'fetchFacilityOrders']);
Route::get('/api/v1/all-orders/{year}/{month}', [OrderApiManagementPage::class, 'fetchAllOrders']);

Route::middleware(['auth:sanctum'])->group(function () {});


require __DIR__ . '/auth.php';
