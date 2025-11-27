<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        $client = Auth::guard('client')->user();

        // ==========================
        // ADMIN GERAL: acesso total
        // ==========================
        if ($admin) {
            return $next($request);
        }

        // ==========================
        // CLIENTE: acesso baseado no plano
        // ==========================
        if ($client && $client->active) {
            $plan = $client->plan;

            // Permissões fixas para todos os clientes
            $access = [
                'home.index'       => true, // Hero Home
                'categories.index' => true,
                'products.index'   => true,
                // Adicione outras rotas específicas se necessário
            ];

            // Obter o nome da rota atual
            $routeName = $request->route()->getName();

            // Verifica se a rota está no array de acesso
            if (isset($access[$routeName]) && !$access[$routeName]) {
                abort(403, 'Você não tem permissão para acessar esta página.');
            }

            return $next($request);
        }

        // ==========================
        // Nenhum usuário autenticado
        // ==========================
        return redirect()->route('client.login')->with('error', 'Você precisa fazer login.');
    }
}
