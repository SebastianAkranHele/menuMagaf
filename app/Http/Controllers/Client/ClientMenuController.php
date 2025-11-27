<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientMenuController extends Controller
{
    /**
     * Página pública do menu digital do cliente
     */
public function index($clientSlug)
{
    // Buscar cliente pelo slug
    $client = Client::where('slug', $clientSlug)->firstOrFail();

    // Hero do cliente
    $hero = $client->homeHero;

    // Categorias e produtos do cliente
    $categories = $client->categories()->with('products')->get();
    $products = $client->products()
                    ->where('stock', '>', 0)
                    ->with('category')
                    ->get();

   $hero = $client->homeHero; // ou getHero($client)
return view('client.menu.index', compact('client', 'categories', 'products', 'hero'));

}

}
