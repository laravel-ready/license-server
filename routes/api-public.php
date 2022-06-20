<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use LaravelReady\LicenseServer\Http\Controllers\Api\AuthController;
use LaravelReady\LicenseServer\Http\Controllers\Api\LicenseValidateController;

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
        ])->post('license', [LicenseValidateController::class, 'licenseValidate']);
    });
