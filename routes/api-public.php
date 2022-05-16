<?php

use Illuminate\Support\Facades\Route;

use LaravelReady\LicenseServer\Http\Controllers\Api\AuthController;

/**
 * Public routes for License Server connector package
 *
 * This routes using for login, list
 */
Route::prefix('license-server')
    ->name('license-server.')
    ->middleware([
        'api',
        'license-server',
        'throttle:10,1',
    ])
    ->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('login', [AuthController::class, 'login'])->name('login');
        });

        Route::middleware([
            'auth:sanctum',
            'sanctum-abilities:license-access',
            // 'abilities:license-access',
        ])->get('xxx', function () {
            return auth()->user();
        });
    });
