<?php

use App\Http\Controllers\Api\ApiServiceController;
use App\Http\Controllers\Api\AuthController;

Route::prefix('/v1')->group(function () {
    Route::prefix('/service-providers')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('api.sp.register');
        Route::post('/login', [AuthController::class, 'SPLogin'])->name('api.sp.login');
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/add-services', [ApiServiceController::class, 'addServices'])
                ->name('api.sp.addServices');

            Route::post('/store-services', [ApiServiceController::class, 'storeService'])
                ->name('sp.addServices.store');

            Route::get('/show-services', [ApiServiceController::class, 'showServices'])
                ->name('api.sp.showServices');

            Route::post('/update-services', [ApiServiceController::class, 'updateService'])
                ->name('sp.addServices.update');
        });
    });
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.sp.logout')
        ->middleware('auth:sanctum');
});
