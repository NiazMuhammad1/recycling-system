<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionItemController;

use App\Http\Controllers\ClientAjaxController;
use App\Http\Controllers\PartnerAjaxController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CatalogAjaxController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollectionPdfController;
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/collections/{collection}/pdf/duty-of-care', [CollectionPdfController::class, 'dutyOfCare'])
    ->name('collections.pdf.duty_of_care');

    Route::get('/collections/{collection}/pdf/hazardous', [CollectionPdfController::class, 'hazardous'])
    ->name('collections.pdf.hazardous');

    Route::get('/collections/{collection}/pdf/data-destruction', [CollectionPdfController::class, 'DataDestruction'])
    ->name('collections.pdf.data_destruction');

    Route::resource('users', UserController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resource('clients', ClientController::class);
    Route::resource('collections', CollectionController::class);

    Route::post('/collection-items/{item}/assign-code',[CollectionItemController::class,'assignCode'])->name('collection-items.assignCode');
    Route::delete('/collection-items/{item}', [CollectionItemController::class,'destroy'])->name('collection-items.destroy');
    Route::delete('/collection-item-codes/{code}',[CollectionItemController::class,'destroyitemcode'])->name('collection-items.codes.destroy');

    /*
    |--------------------------------------------------------------------------
    | Collection Items (bulk/edit/update)
    |--------------------------------------------------------------------------
    */
    Route::get('collections/{collection}/items/edit', [CollectionItemController::class, 'edit'])
        ->name('collections.items.edit');

    Route::post('collections/{collection}/items/bulk', [CollectionItemController::class, 'bulkStore'])
        ->name('collections.items.bulkStore');

    // Route::put('collections/{collection}/items', [CollectionItemController::class, 'update'])
    //     ->name('collections.items.update');

    Route::put('/collections/{collection}/items', [CollectionItemController::class,'updateGrid'])
    ->name('collections.items.update');

    Route::put('/collections/{collection}/items', [CollectionItemController::class,'updateGrid'])
    ->name('collections.items.update');

    Route::delete('/collections/items/{item}', [CollectionItemController::class,'destroy'])
        ->name('collections.items.destroy');

    /*
    |--------------------------------------------------------------------------
    | Collect step
    |--------------------------------------------------------------------------
    */
    Route::get('collections/{collection}/collect', [CollectionItemController::class, 'collectForm'])
        ->name('collections.collect.form');

    Route::post('collections/{collection}/collect', [CollectionItemController::class, 'collectSave'])
        ->name('collections.collect.save');

    /*
    |--------------------------------------------------------------------------
    | Process step
    |--------------------------------------------------------------------------
    */
    Route::get('collections/{collection}/process', [CollectionItemController::class, 'processIndex'])
    ->name('collections.process.index');

    Route::post('collections/{collection}/process/bulk', [CollectionController::class, 'bulkProcess'])
        ->name('collections.process.bulk');

    Route::get('collections/{collection}/process/{item}', [CollectionItemController::class, 'processItemForm'])
        ->name('collections.process.itemForm');

    Route::post('collections/{collection}/process/{item}', [CollectionItemController::class, 'processItemSave'])
        ->name('collections.process.itemSave');
    
   

    /*
    |--------------------------------------------------------------------------
    | AJAX
    |--------------------------------------------------------------------------
    */
    Route::get('/ajax/clients/select2', [ClientAjaxController::class, 'select2'])
        ->name('ajax.clients.select2');

    Route::get('/ajax/clients/{client}', [ClientAjaxController::class, 'show'])
        ->name('ajax.clients.show');

    Route::get('/ajax/partners/select2', [PartnerAjaxController::class, 'select2'])->name('ajax.partners.select2');
    Route::post('/ajax/partners', [PartnerAjaxController::class, 'store'])->name('ajax.partners.store');
    Route::get('/ajax/partners/{partner}', [PartnerAjaxController::class, 'show'])->name('ajax.partners.show');

    // Catalog (keep ONE source of truth: CatalogAjaxController)
    Route::get('/ajax/manufacturers', [CatalogAjaxController::class, 'manufacturers'])
        ->name('ajax.manufacturers');

    Route::get('/ajax/manufacturers/{manufacturer}/models', [CatalogAjaxController::class, 'models'])
        ->name('ajax.manufacturers.models');

    // Category -> manufacturers (keep ONE)
    Route::get('/ajax/categories/{category}/manufacturers', [AjaxController::class, 'manufacturersByCategory'])
        ->name('ajax.categories.manufacturers');

    // select2 “tag create”
    Route::post('/ajax/manufacturers', [AjaxController::class, 'storeManufacturer'])
        ->name('ajax.manufacturers.store');

    Route::post('/ajax/models', [AjaxController::class, 'storeProductModel'])
        ->name('ajax.models.store');
});
