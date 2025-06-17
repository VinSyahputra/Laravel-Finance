<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Livewire\Dashboard;
use App\Livewire\Setting\Category;
use App\Livewire\Setting\Role;
use App\Livewire\Setting\User;
use App\Livewire\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    // Show login page for guests
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect '/' to '/dashboard' for authenticated users
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', Transaction::class)->name('transaction');
});

// Route::prefix('transactions')
//     ->middleware(['auth', 'verified'])
//     ->controller(TransactionController::class)
//     ->group(function () {
//         Route::get('/', 'index');
//     });

Route::prefix('settings')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/categories', Category::class)->name('settings.category');
        Route::get('/roles', Role::class)->name('settings.role');
        Route::get('/users', User::class)->name('settings.user');
    });


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
