<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Prospects routes
    Route::resource('prospects', ProspectController::class);
    Route::post('/prospects/{prospect}/lead', [ProspectController::class, 'storeLead'])
        ->name('prospects.lead');
    Route::get('/prospects-export', [ProspectController::class, 'export'])->name('prospects.export');
    
    // API routes (usando autenticação por sessão)
    Route::prefix('api')->group(function () {
        Route::get('/cities/search', [CityController::class, 'search'])->name('api.cities.search');
        Route::get('/prospects/check-new', [ProspectController::class, 'checkNew'])->name('api.prospects.check-new');
    });
    
    // Plan route
    Route::get('/meu-plano', function () {
        return view('plan.index');
    })->name('plan.index');
});

// Super Admin routes
Route::middleware(['auth', 'super.admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::get('/searches', [SuperAdminController::class, 'searches'])->name('searches');
    Route::get('/payments', [SuperAdminController::class, 'payments'])->name('payments');
    Route::post('/payments', [SuperAdminController::class, 'storePayment'])->name('payments.store');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/users/{user}/results-limit', [SuperAdminController::class, 'updateUserResultsLimit'])->name('users.update-results-limit');
});

require __DIR__.'/auth.php';
