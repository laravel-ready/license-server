<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

use LaravelReady\LicenseServer\Http\Controllers\Api\AuthController;
use LaravelReady\LicenseServer\Http\Controllers\Api\LicenseController;

Route::prefix('api/license-server')
    ->name('license-server.')
    ->middleware(['api'])
    ->group(function () {
        Route::resource('licenses', LicenseController::class)
            ->middleware(Config::get('license-server.admin_api_middleware', ['auth:sanctum']))
            ->names('licenses');
    });
