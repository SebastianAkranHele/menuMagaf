<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Visit;
use Carbon\Carbon;

class MenuController extends Controller
{
   public function index()
{
    $categories = Category::all();
       // --- FILTRA APENAS PRODUTOS DISPONÍVEIS ---
        $products = Product::with('category')
                    ->where('stock', '>', 0)
                    ->get();

    // --- REGISTRAR VISITA ---
    $ip = request()->ip();
    $userAgent = request()->header('User-Agent');
    $today = Carbon::today();

    // Só cria se ainda não existir visita desse IP hoje
    $alreadyVisited = Visit::where('ip', $ip)
        ->whereDate('created_at', $today)
        ->exists();

    if (!$alreadyVisited) {
        Visit::create([
            'ip'         => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    return view('menu', compact('categories', 'products'));
}
}
