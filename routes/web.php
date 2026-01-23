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
    Route::get('/ajax/clients/select2', [ClientAjaxController::class, 'select2'])->name('ajax.clients.select2');
    Route::get('/ajax/clients/{client}', [ClientAjaxController::class, 'show'])->name('ajax.clients.show');

    Route::post('/collections/{collection}/items/bulk', [CollectionItemController::class, 'bulkStore'])
    ->name('collections.items.bulkStore');

    Route::get('/ajax/manufacturers/{manufacturer}/models', function (\App\Models\Manufacturer $manufacturer) {
    return $manufacturer->productModels()
        ->where('is_active', 1)
        ->orderBy('name')
        ->get(['id','name'])
        ->map(fn($m) => ['id' => $m->id, 'text' => $m->name]);
})->name('ajax.manufacturers.models');
});
