<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    
    // Registration (owner only)
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->middleware('role:owner')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:owner');
    
    // Products
    Route::resource('products', ProductController::class)->middleware('role:owner');
    
    // Categories
    Route::resource('categories', CategoryController::class)->middleware('role:owner');
    
    // Expenses
    Route::resource('expenses', ExpenseController::class)->middleware('role:owner');
    
    // Cashier/POS
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier');
    Route::get('/cashier/products', [CashierController::class, 'getProducts'])->name('cashier.products');
    Route::post('/cashier/process-order', [CashierController::class, 'processOrder'])->name('cashier.process-order');
    Route::get('/cashier/orders', [CashierController::class, 'orderHistory'])->name('cashier.orders');
    Route::get('/cashier/orders/{order}', [CashierController::class, 'viewOrder'])->name('cashier.view-order');
    
    // Reports (owner only)
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/busy-hours', [ReportController::class, 'busyHoursReport'])->name('reports.busy-hours');
    });
});
