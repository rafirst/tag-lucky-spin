<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\WheelController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [WheelController::class, 'index'])->name('wheel');
    Route::post('/spin', [WheelController::class, 'spin'])->name('spin');
    Route::delete('/winners', [WheelController::class, 'clearWinners'])->name('winners.clear');

    Route::controller(PrizeController::class)->prefix('prizes')->name('prizes.')->group(function () {
        Route::post('/', 'store')->name('store');
        Route::put('/reorder', 'reorder')->name('reorder');   // harus di atas {prize}
        Route::delete('/', 'clear')->name('clear');
        Route::patch('/{prize}', 'update')->name('update');
        Route::delete('/{prize}', 'destroy')->name('destroy');
    });
});
