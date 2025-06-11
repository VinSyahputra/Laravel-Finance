<?php

use App\Http\Controllers\Api\AnalyticController;
use App\Http\Controllers\Api\Setting\CategoryController;
use App\Http\Controllers\Api\Setting\PermissionController;
use App\Http\Controllers\Api\Setting\RoleController;
use App\Http\Controllers\Api\Setting\UserController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;


Route::get('categories', [CategoryController::class, 'index'])
    ->withoutMiddleware('auth:sanctum');


Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'update', 'destroy']);

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
    ->group(function () {

        Route::resource('roles', RoleController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::resource('permissions', PermissionController::class)->only(['index','show']);

        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::resource('categories', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    });
