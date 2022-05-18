<?php

use Illuminate\Support\Facades\Route;

use LaravelReady\LicenseServer\Services\LicenseService;
use LaravelReady\LicenseServer\Http\Controllers\Api\AuthController;

/**
 * Public routes for License Server connector package
 *
 * This routes using for login, list
 */
Route::prefix('api/license-server')
    ->name('license-server.')
    ->middleware([
        'api',
        'ls-domain-guard',
        'throttle:60,1',
    ])
    ->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('login', [AuthController::class, 'login'])->name('login');
        });

        Route::middleware([
            'auth:sanctum',
            'ls-license-guard',
        ])->get('license', function () {
            $license = auth()->user();

            unset($license['id']);
            unset($license['user_id']);
            unset($license['created_by']);

            return $license;
        });
    });
