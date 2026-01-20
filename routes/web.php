<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ClientAjaxController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resource('clients', ClientController::class);
    Route::resource('collections', CollectionController::class);

    // Select2 client search + inline create
    Route::get('ajax/clients', [ClientAjaxController::class, 'select2'])->name('ajax.clients.select2');
    Route::post('ajax/clients', [ClientAjaxController::class, 'store'])->name('ajax.clients.store');
});
