<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeHeroController as AdminHomeHeroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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




Route::post('/check-access-code', function (Request $request) {
    $code = $request->input('code');
    $validCode = env('ADMIN_ACCESS_CODE');

    if ($code === $validCode) {
        // 🔹 Guardar flag na sessão
        Session::put('admin_access_granted', true);
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false], 401);
});;

