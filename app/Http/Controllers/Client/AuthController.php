<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login'); // cria a view
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('client')->attempt($credentials)) {
            // Pega o cliente autenticado
            $client = Auth::guard('client')->user();

            // Verifica se a conta está ativa
            if (!$client->active) {
                Auth::guard('client')->logout();
                return back()->withErrors(['email' => 'Sua conta não está ativa.']);
            }

            // Redireciona para o dashboard do cliente
            return redirect()->route('client.dashboard');
        }

        // Credenciais inválidas
        return back()->withErrors(['email' => 'Credenciais inválidas']);
    }

    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect()->route('client.login');
    }
}
