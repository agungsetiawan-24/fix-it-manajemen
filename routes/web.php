<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceTicketController;
use App\Http\Controllers\TvDisplayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route: directly redirects to dashboard (if authenticated) or login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// PUBLIC ROUTES (No Login Required)
// ==========================================

// 1. Customer Tracking Portal
Route::get('/tracking', [CustomerPortalController::class, 'index'])->name('tracking.index');
Route::get('/cek-servis', [CustomerPortalController::class, 'index']); // Alias
Route::get('/track/{ticket_code}', [CustomerPortalController::class, 'show'])->name('tracking.detail');
Route::get('/tracking/{ticket_code}', [CustomerPortalController::class, 'show']); // Alias
Route::post('/track/{ticket_code}/approve', [CustomerPortalController::class, 'approve'])->name('tracking.approve');
Route::post('/track/{ticket_code}/reject', [CustomerPortalController::class, 'reject'])->name('tracking.reject');

// 2. TV Display Waiting Room & Real-time Queue Board
Route::get('/tv-display', [TvDisplayController::class, 'index'])->name('tv.display');
Route::get('/queue-board', [TvDisplayController::class, 'index']); // Alias
Route::get('/api/queue-board', [TvDisplayController::class, 'queueData'])->name('tv.api');

// ==========================================
// PROTECTED ROUTES (Admin & Teknisi)
// ==========================================
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Service Tickets Lifecycle
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [ServiceTicketController::class, 'index'])->name('index');
        Route::get('/create', [ServiceTicketController::class, 'create'])->name('create');
        Route::post('/', [ServiceTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [ServiceTicketController::class, 'show'])->name('show');
        Route::patch('/{ticket}/status', [ServiceTicketController::class, 'updateStatus'])->name('update-status');
        Route::get('/{ticket}/receipt', [ServiceTicketController::class, 'printReceipt'])->name('receipt');
    });
});
