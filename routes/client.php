<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\CategoryController as ClientCategoryController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Client\HomeHeroController as ClientHomeHeroController;
use App\Http\Controllers\Client\ClientMenuController as ClientMenuController;

// =========================================================
// ROTAS DO CLIENTE (PAINEL + LOGIN)
// =========================================================
Route::prefix('client')->name('client.')->group(function () {

    // ----------------------------
    // Login / Logout
    // ----------------------------
    Route::get('login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [ClientAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [ClientAuthController::class, 'logout'])->name('logout');

    // ----------------------------
    // Dashboard protegido
    // ----------------------------
    Route::middleware(['client.auth'])->group(function () {

        // Dashboard principal
        Route::get('dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

        // ----------------------------
        // Hero Home
        // ----------------------------
        Route::get('home', [ClientHomeHeroController::class, 'index'])->name('home.index');
        Route::get('home/hero', [ClientHomeHeroController::class, 'edit'])->name('home.edit');
        Route::put('home/hero', [ClientHomeHeroController::class, 'update'])->name('home.update');

        // ----------------------------
        // Categorias
        // ----------------------------
        Route::get('categories', [ClientCategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [ClientCategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [ClientCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [ClientCategoryController::class, 'destroy'])->name('categories.destroy');

        // ----------------------------
        // Produtos
        // ----------------------------
        Route::get('products', [ClientProductController::class, 'index'])->name('products.index');
        Route::post('products', [ClientProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ClientProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ClientProductController::class, 'destroy'])->name('products.destroy');



    });

});


// =========================================================
// ROTA PÚBLICA DO CLIENTE (FORA DO PREFIX)
// =========================================================
Route::get('/{clientSlug?}', [ClientHomeHeroController::class, 'publicHome'])
    ->name('client.home.public');

// Página pública do menu digital do cliente
Route::get('/{clientSlug}/menu', [ClientMenuController::class, 'index'])->name('client.menu.public');
