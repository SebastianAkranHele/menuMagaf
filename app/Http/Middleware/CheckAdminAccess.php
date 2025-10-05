<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 🔹 Verifica se o acesso foi autorizado pela sessão
        if (!Session::get('admin_access_granted')) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
