<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

Route::prefix('v1')
    ->middleware('web')->group(function () {
        require __DIR__ . '/api/auth.php';
    });
