<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SalesScriptController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminSalesScriptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'redirect.if.super.admin', 'check.user.active'])->name('dashboard');

Route::middleware(['auth', 'redirect.if.super.admin', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Prospects routes
    Route::resource('prospects', ProspectController::class);
    Route::post('/prospects/{prospect}/lead', [ProspectController::class, 'storeLead'])
        ->name('prospects.lead');
    Route::get('/prospects-export', [ProspectController::class, 'export'])->name('prospects.export');
    
    // Saved Searches routes
    Route::get('/minhas-pesquisas', [ProspectController::class, 'mySearches'])->name('searches.my');
    Route::get('/pesquisas/{searchId}/exportar-csv', [ProspectController::class, 'exportSearchCsv'])->name('searches.export.csv');
    Route::get('/pesquisas/{searchId}/exportar-xlsx', [ProspectController::class, 'exportSearchXlsx'])->name('searches.export.xlsx');
    
    // API routes (usando autenticação por sessão)
    Route::prefix('api')->group(function () {
        Route::get('/cities/search', [CityController::class, 'search'])->name('api.cities.search');
        Route::get('/prospects/check-new', [ProspectController::class, 'checkNew'])->name('api.prospects.check-new');
    });
    
    // Plan route
    Route::get('/meu-plano', function () {
        return view('plan.index');
    })->name('plan.index');
    
    // Sales Scripts routes
    Route::get('/scripts-de-vendas', [SalesScriptController::class, 'index'])->name('sales-scripts.index');
    Route::get('/scripts-de-vendas/buscar', [SalesScriptController::class, 'search'])->name('sales-scripts.search');
    Route::get('/scripts-de-vendas/categoria/{category}', [SalesScriptController::class, 'showCategory'])->name('sales-scripts.category');
    Route::get('/scripts-de-vendas/script/{script}', [SalesScriptController::class, 'show'])->name('sales-scripts.show');
});

// Super Admin routes
Route::middleware(['auth', 'super.admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/saas-dashboard', [SuperAdminController::class, 'saasDashboard'])->name('saas-dashboard');
    
    // Users
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/refund', [SuperAdminController::class, 'toggleRefund'])->name('users.refund');
    Route::post('/users/{user}/impersonate', [SuperAdminController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/impersonation/leave', [SuperAdminController::class, 'leaveImpersonation'])->name('impersonation.leave');
    Route::post('/users/{user}/plan', [SuperAdminController::class, 'updateUserPlan'])->name('users.update-plan');
    Route::get('/users/{user}/login-history', [SuperAdminController::class, 'userLoginHistory'])->name('users.login-history');
    Route::get('/users/{user}/modules', [SuperAdminController::class, 'userModules'])->name('users.modules');
    Route::post('/users/{user}/modules', [SuperAdminController::class, 'updateUserModules'])->name('users.update-modules');
    Route::post('/users/{user}/results-limit', [SuperAdminController::class, 'updateUserResultsLimit'])->name('users.update-results-limit');
    
    // Reports
    Route::get('/reports/users-not-logged-in', [SuperAdminController::class, 'reportsUsersNotLoggedIn'])->name('reports.users-not-logged-in');
    Route::get('/reports/users-logged-in-today', [SuperAdminController::class, 'reportsUsersLoggedInToday'])->name('reports.users-logged-in-today');
    Route::get('/reports/user-login-counts', [SuperAdminController::class, 'reportsUserLoginCounts'])->name('reports.user-login-counts');
    
    // Queues
    Route::get('/queues', [SuperAdminController::class, 'queues'])->name('queues');
    Route::post('/queues/pause', [SuperAdminController::class, 'pauseQueue'])->name('queues.pause');
    Route::post('/queues/resume', [SuperAdminController::class, 'resumeQueue'])->name('queues.resume');
    
    // Costs
    Route::get('/costs', [SuperAdminController::class, 'costs'])->name('costs');
    
    // Logs
    Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
    
    // Plans
    Route::get('/plans', [SuperAdminController::class, 'plans'])->name('plans');
    Route::post('/plans', [SuperAdminController::class, 'createPlan'])->name('plans.store');
    Route::post('/plans/{plan}', [SuperAdminController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/{plan}', [SuperAdminController::class, 'deletePlan'])->name('plans.delete');
    
    // Other
    Route::get('/searches', [SuperAdminController::class, 'searches'])->name('searches');
    Route::get('/payments', [SuperAdminController::class, 'payments'])->name('payments');
    Route::post('/payments', [SuperAdminController::class, 'storePayment'])->name('payments.store');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    
    // Sales Scripts Management
    Route::prefix('sales-scripts')->name('sales-scripts.')->group(function () {
        Route::get('/', [SuperAdminSalesScriptController::class, 'index'])->name('index');
        
        // Categories
        Route::get('/categorias/criar', [SuperAdminSalesScriptController::class, 'createCategory'])->name('categories.create');
        Route::post('/categorias', [SuperAdminSalesScriptController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categorias/{category}', [SuperAdminSalesScriptController::class, 'showCategory'])->name('categories.show');
        Route::get('/categorias/{category}/editar', [SuperAdminSalesScriptController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categorias/{category}', [SuperAdminSalesScriptController::class, 'updateCategory'])->name('categories.update');
        
        // Scripts
        Route::get('/categorias/{category}/scripts/criar', [SuperAdminSalesScriptController::class, 'createScript'])->name('scripts.create');
        Route::post('/categorias/{category}/scripts', [SuperAdminSalesScriptController::class, 'storeScript'])->name('scripts.store');
        Route::get('/scripts/{script}/editar', [SuperAdminSalesScriptController::class, 'editScript'])->name('scripts.edit');
        Route::put('/scripts/{script}', [SuperAdminSalesScriptController::class, 'updateScript'])->name('scripts.update');
        Route::delete('/scripts/{script}', [SuperAdminSalesScriptController::class, 'destroyScript'])->name('scripts.destroy');
        Route::post('/scripts/{script}/toggle-status', [SuperAdminSalesScriptController::class, 'toggleScriptStatus'])->name('scripts.toggle-status');
    });
});

require __DIR__.'/auth.php';
