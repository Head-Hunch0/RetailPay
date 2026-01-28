<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', [UserController::class, 'showLogin'])->name('login');
Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');

Route::middleware('auth')->group(function () {
	Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // dashboard routes
	Route::get('/dashboard', [MainController::class, 'index'])->name('dashboard');
    // branches routes
	Route::get('/branches', [MainController::class, 'branches'])->name('branches.index');
    // stores routes
	Route::get('/stores', [MainController::class, 'stores'])->name('stores.index');
    // products routes
	Route::get('/products', [ProductController::class, 'index'])->name('products.index');
	Route::post('/products', [ProductController::class, 'store'])->name('products.store');
	Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
	Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    // stock routes
	Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    // sales routes
	Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    // transfers routes
	Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
	Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
	Route::patch('/transfers/{transfer}', [TransferController::class, 'update'])->name('transfers.update');
});