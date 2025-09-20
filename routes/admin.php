<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeHeroController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Rotas de autenticação
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Rotas protegidas
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Hero
        Route::get('home', [HomeHeroController::class, 'index'])->name('home.index');
        Route::get('home/hero/edit', [HomeHeroController::class, 'edit'])->name('home.hero.edit');
        Route::put('home/hero/update', [HomeHeroController::class, 'update'])->name('home.hero.update');

        // Categorias
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Produtos
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Pedidos
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

        Route::post('orders/{order}/complete', [\App\Http\Controllers\Admin\OrderController::class, 'complete'])->name('orders.complete');
        Route::post('orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/restore-pending', [\App\Http\Controllers\Admin\OrderController::class, 'restorePending'])->name('orders.restorePending');

        Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');

        // Exportações
        Route::get('orders/export/pdf', [\App\Http\Controllers\Admin\OrderController::class, 'exportPdf'])->name('orders.export.pdf');
        Route::get('orders/{order}/export/pdf', [\App\Http\Controllers\Admin\OrderController::class, 'exportSinglePdf'])->name('orders.export.single');
        Route::get('orders/{order}/export/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'exportOrderPdf'])->name('orders.export.invoice');

        Route::get('orders/export/excel', [\App\Http\Controllers\Admin\OrderController::class, 'exportExcel'])->name('orders.export.excel');
        Route::get('orders/export/csv', [\App\Http\Controllers\Admin\OrderController::class, 'exportCsv'])->name('orders.export.csv');

        Route::get('orders/{order}/export/excel', [\App\Http\Controllers\Admin\OrderController::class, 'exportSingleExcel'])->name('orders.export.single.excel');
Route::get('orders/{order}/export/csv', [\App\Http\Controllers\Admin\OrderController::class, 'exportSingleCsv'])->name('orders.export.single.csv');

    });
});
