<?php

use App\Http\Controllers\Api\AnalyticController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;


Route::prefix('categories')
    ->middleware('auth:sanctum')
    ->controller(CategoryController::class)
    ->group(function () {
        Route::get('/', 'index')->withoutMiddleware('auth:sanctum');
        Route::post('/', 'store');
        Route::put('{categoryId}', 'update');
        Route::delete('{categoryId}', 'destroy');
    });

Route::prefix('transactions')
    ->middleware('auth:sanctum')
    ->controller(TransactionController::class)
    ->group(function () {
        Route::get('/', 'index')->withoutMiddleware('auth:sanctum');
        Route::post('/', 'store');
        Route::put('{categoryId}', 'update');
        Route::delete('{categoryId}', 'destroy');
    });

Route::prefix('analytics')
    ->middleware('auth:sanctum')
    ->controller(AnalyticController::class)
    ->group(function () {
        Route::get('/this-year', 'getDataThisYear');
        Route::get('/recent-transactions', 'getDataRecentTransactions');
    });
