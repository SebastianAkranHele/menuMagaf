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
           // Antes: retornava a view direto
    // Route::get('/dashboard', function () {
    //     return view('admin.dashboard');
    // })->name('dashboard');

    // Agora: chama o controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Hero
        Route::get('home', [HomeHeroController::class, 'index'])->name('home.index'); // visualiza a home no dashboard
        Route::get('home/hero/edit', [HomeHeroController::class, 'edit'])->name('home.hero.edit');
        Route::put('home/hero/update', [HomeHeroController::class, 'update'])->name('home.hero.update');

        // Categorias
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index'); // lista
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create'); // criar
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store'); // salvar
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit'); // editar
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update'); // atualizar
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // deletar

            // Produtos
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Pedidos
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/complete', [\App\Http\Controllers\Admin\OrderController::class, 'complete'])->name('orders.complete');
        Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
       


    });
});
