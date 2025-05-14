<?php

use App\Http\Controllers\Api\AnalyticController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SettingController;
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
        Route::get('/expense-this-year', 'getDataExpenseThisYear');
        Route::get('/income-this-year', 'getDataIncomeThisYear');
        Route::get('/expense-this-month', 'getDataExpenseThisMonth');
        Route::get('/income-this-month', 'getDataIncomeThisMonth');
        Route::get('/recent-expenses', 'getDataRecentExpenses');
        Route::get('/recent-incomes', 'getDataRecentIncomes');
        Route::get('/balance', 'getBalance');
    });

Route::prefix('settings')
    ->middleware('auth:sanctum')
    ->controller(SettingController::class)
    ->group(function () {
        Route::get('/roles', 'getRoles')->withoutMiddleware('auth:sanctum');
        Route::post('/roles', 'storeRole');
        Route::get('/roles/{roleId}', 'getPermissionsByRole');
        Route::get('/permissions', 'getPermissions');
    });
