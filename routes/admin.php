<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeHeroController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderExportController;
use App\Http\Controllers\Admin\ReportController;

Route::prefix('admin')->name('admin.')->group(function () {
    // ============================
    // Autenticação
    // ============================
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ============================
    // Rotas protegidas
    // ============================
    Route::middleware(['admin.auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ============================
        // Hero da Home
        // ============================
        Route::get('home', [HomeHeroController::class, 'index'])->name('home.index');
        Route::get('home/hero/edit', [HomeHeroController::class, 'edit'])->name('home.hero.edit');
        Route::put('home/hero/update', [HomeHeroController::class, 'update'])->name('home.hero.update');

        // ============================
        // Categorias
        // ============================
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // ============================
        // Produtos
        // ============================
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // ============================
        // Pedidos
        // ============================
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::post('orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/restore-pending', [OrderController::class, 'restorePending'])->name('orders.restorePending');

        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // ============================
        // Exportações - Todos os Pedidos
        // ============================
        Route::get('orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
        Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
        Route::get('orders/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');

        // ============================
        // Exportações - Pedido único
        // ============================
        Route::get('orders/{order}/export/pdf', [OrderController::class, 'exportSinglePdf'])->name('orders.export.single');
        Route::get('orders/{order}/export/invoice', [OrderController::class, 'exportOrderPdf'])->name('orders.export.invoice');
        Route::get('orders/{order}/export/excel', [OrderController::class, 'exportSingleExcel'])->name('orders.export.single.excel');
        Route::get('orders/{order}/export/csv', [OrderController::class, 'exportSingleCsv'])->name('orders.export.single.csv');

        // ============================
        // Exportações - Produto específico dentro do Pedido
        // ============================
        Route::get('orders/{order}/export/product/{product}/pdf', [OrderExportController::class, 'exportProductPdf'])->name('orders.export.product.pdf');
        Route::get('orders/{order}/export/product/{product}/excel', [OrderExportController::class, 'exportProductExcel'])->name('orders.export.product.excel');
        Route::get('orders/{order}/export/product/{product}/csv', [OrderExportController::class, 'exportProductCsv'])->name('orders.export.product.csv');

        // ============================
        // Relatórios
        // ============================
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/products', [ReportController::class, 'products'])->name('products');
            Route::get('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');

            // Exportar PDF de um pedido específico
            Route::get('/export/pdf/{order}', [ReportController::class, 'exportSinglePdf'])
                ->name('export.pdf.single');

            // Opcional: exportar Excel ou CSV de um pedido específico
            Route::get('/export/excel/{order}', [ReportController::class, 'exportSingleExcel'])
                ->name('export.excel.single');

            Route::get('/export/csv/{order}', [ReportController::class, 'exportSingleCsv'])
                ->name('export.csv.single');

         });
    });
});
