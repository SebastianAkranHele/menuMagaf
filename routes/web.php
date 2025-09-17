<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeHeroController as AdminHomeHeroController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
require __DIR__.'/admin.php';


Route::get('/', [AdminHomeHeroController::class, 'publicHome'])->name('home');

Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



