<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;


Route::prefix('categories')
    ->controller(CategoryController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('{categoryId}', 'update');
        Route::delete('{categoryId}', 'destroy');
    });

Route::prefix('transactions')
    ->controller(TransactionController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('{categoryId}', 'update');
        Route::delete('{categoryId}', 'destroy');
    });
