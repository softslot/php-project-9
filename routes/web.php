<?php

use App\Http\Controllers\UrlsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('urls', [UrlsController::class, 'index'])
    ->name('urls.index');

Route::post('urls', [UrlsController::class, 'store'])
    ->name('urls.store');

Route::get('urls/{id}', [UrlsController::class, 'show'])
    ->name('urls.show');
