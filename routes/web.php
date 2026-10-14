<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Authenticated Web Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Case Management Web CRUD Routes
    Route::prefix('cases')->name('cases.')->group(function () {
        Route::get('/', [CaseController::class, 'index'])->name('index');
        Route::get('/create', [CaseController::class, 'create'])->name('create');
        Route::post('/', [CaseController::class, 'store'])->name('store');
        Route::get('/{id}', [CaseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CaseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CaseController::class, 'destroy'])->name('destroy');
    });
});
