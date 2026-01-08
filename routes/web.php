<?php

use Illuminate\Support\Facades\Route;
use Woub\Http\Controllers\PageController;

Route::get('/login', [PageController::class, 'login'])->name('login');

Route::middleware('auth:web')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('home');
    Route::get('/explore', [PageController::class, 'explore'])->name('explore');
    Route::get('/cities', [PageController::class, 'cities'])->name('cities');
});
