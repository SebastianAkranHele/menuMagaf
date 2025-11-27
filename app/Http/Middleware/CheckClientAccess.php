<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        $client = Auth::guard('client')->user(); // guard do cliente

        if (!$client || !$client->active) {
            return redirect()->route('client.auth.login')->with('error', 'Você precisa fazer login.');
        }

        // Define permissões dinamicamente
        $plan = $client->plan;

        $access = [
            'hero_home'  => $plan->max_categories > 0,
            'categories' => $plan->max_categories > 0,
            'products'   => $plan->max_products > 0,
            // Adicione aqui novas permissões
        ];

        if ($permission && empty($access[$permission])) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
