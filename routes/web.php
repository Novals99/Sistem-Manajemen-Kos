<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Guest Routes ─────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// ── Authenticated Routes ─────────────────────────────
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');

    // ── Staff Routes (Owner & Admin) ─────────────────
    Route::middleware('role:owner,admin')->group(function () {
        // Rooms
        // Route::resource('rooms', \App\Http\Controllers\RoomController::class);

        // Tenants
        // Route::resource('tenants', \App\Http\Controllers\TenantController::class);

        // Reports
        // Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

        // Activity Log
        // Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // ── All Authenticated Users ──────────────────────
    // Bookings
    // Route::resource('bookings', \App\Http\Controllers\BookingController::class);

    // Payments
    // Route::resource('payments', \App\Http\Controllers\PaymentController::class);

    // Maintenance
    // Route::resource('maintenances', \App\Http\Controllers\MaintenanceController::class);
});
