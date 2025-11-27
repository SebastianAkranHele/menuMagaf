<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClientAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.auth.login')
                ->with('error', 'Você precisa fazer login.');
        }

        return $next($request);
    }
}
