<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\HomeHero;

class ClientBaseController extends Controller
{
    protected $client;
    protected $hero;

    public function __construct()
    {
        // Pega o cliente autenticado
        $this->client = auth('client')->user();

        if (!$this->client) {
            abort(403, 'Cliente não autenticado.');
        }

        // Hero do cliente
        $this->hero = $this->client->homeHero ?? new HomeHero();

        // Compartilha com todas as views do cliente
        view()->share([
            'client' => $this->client,
            'hero'   => $this->hero,
        ]);
    }
}
