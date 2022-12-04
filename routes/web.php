<?php

use App\Http\Controllers\UrlChecksController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::controller(UrlController::class)
    ->name('urls.')
    ->group(function () {
        Route::get('urls', 'index')->name('index');
        Route::post('urls', 'store')->name('store');
        Route::get('urls/{id}', 'show')->name('show');
    });

Route::controller(UrlChecksController::class)
    ->name('url_checks.')
    ->group(function () {
        Route::post('urls/{id}/checks', 'store')->name('store');
    });
