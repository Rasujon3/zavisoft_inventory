<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DurationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WorkingDayController;
use App\Http\Controllers\WorkingTimeRangeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'loginPage'])->name('login-page');

Route::get('/admin/login', [IndexController::class, 'loginPage'])->name('login-admin');

Route::post('admin-login', [AccessController::class, 'adminLogin'])->name('admin-login');

Route::get('/logout', [AccessController::class, 'Logout'])->name('logout');

Route::group(['middleware' => ['prevent-back-history', 'admin_auth']], function () {
    // admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');
    Route::get('password-change', [AccessController::class, 'passwordChange'])->name('password-change');
    Route::post('change-password', [AccessController::class, 'changePassword'])->name('change-password');

    // Products
    Route::resource('products', ProductController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Sales
    Route::resource('sales', SaleController::class);
});


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');

    return 'All caches (config, route, view, optimize) have been cleared!';
});

Route::get('/migrate', function(){
    Artisan::call('migrate', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Migrations run successfully.']);
});

Route::get('/db-seed', function(){
    Artisan::call('db:seed', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Database seeded successfully.']);
});
