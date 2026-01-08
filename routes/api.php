<?php

use Woub\City\Infrastructure\CityController;
use Woub\User\Infrastructure\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:web')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/logout', [UserController::class, 'logout']);
    
    Route::get('/cities/{city}/search', [CityController::class, 'search']);
    Route::get('/cities/{city}/details', [CityController::class, 'get']);

    Route::get('/cities', [CityController::class, 'index']);
    Route::post('/cities', [CityController::class, 'like']);
    Route::delete('/cities/{cityId}', [CityController::class, 'unlike']);

    Route::get('/cities/image/{photoPath}', [CityController::class, 'proxyImage']);
});
