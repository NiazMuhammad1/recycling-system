<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ClientAjaxController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CollectionItemController;
use App\Http\Controllers\Ajax\CatalogAjaxController;
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resource('clients', ClientController::class);


    Route::post('/collections/{collection}/items/bulk', [CollectionItemController::class, 'bulkStore'])
    ->name('collections.items.bulkStore');

    Route::resource('collections', CollectionController::class);

    // Items inside collection
    Route::get('collections/{collection}/items/edit', [CollectionItemController::class,'edit'])
        ->name('collections.items.edit');
    Route::post('collections/{collection}/items/bulk', [CollectionItemController::class,'bulkStore'])
        ->name('collections.items.bulkStore');
    Route::put('collections/{collection}/items', [CollectionItemController::class,'update'])
        ->name('collections.items.update');

    // Collect step
    Route::get('collections/{collection}/collect', [CollectionItemController::class,'collectForm'])
        ->name('collections.collect.form');
    Route::post('collections/{collection}/collect', [CollectionItemController::class,'collectSave'])
        ->name('collections.collect.save');

    // Process step
    Route::get('collections/{collection}/process', [CollectionItemController::class,'processIndex'])
        ->name('collections.process.index');
    Route::get('collections/{collection}/process/{item}', [CollectionItemController::class,'processItemForm'])
        ->name('collections.process.itemForm');
    Route::post('collections/{collection}/process/{item}', [CollectionItemController::class,'processItemSave'])
        ->name('collections.process.itemSave');

    // AJAX
    Route::get('/ajax/clients/select2', [ClientAjaxController::class,'select2'])->name('ajax.clients.select2');
    Route::get('/ajax/clients/{client}', [ClientAjaxController::class,'show'])->name('ajax.clients.show');

    Route::get('/ajax/manufacturers', [CatalogAjaxController::class,'manufacturers'])->name('ajax.manufacturers');
    Route::get('/ajax/manufacturers/{manufacturer}/models', [CatalogAjaxController::class,'models'])->name('ajax.manufacturers.models');

    Route::get('/ajax/categories/{category}/manufacturers', [AjaxController::class, 'manufacturersByCategory'])
    ->name('ajax.categories.manufacturers');

    Route::get('/ajax/manufacturers/{manufacturer}/models', [AjaxController::class, 'modelsByManufacturer'])
        ->name('ajax.manufacturers.models');


    Route::get('/ajax/manufacturers/{manufacturer}/models', function (\App\Models\Manufacturer $manufacturer) {
    return $manufacturer->productModels()
        ->where('is_active', 1)
        ->orderBy('name')
        ->get(['id','name'])
        ->map(fn($m) => ['id' => $m->id, 'text' => $m->name]);
})->name('ajax.manufacturers.models');
});
