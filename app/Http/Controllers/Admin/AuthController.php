<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Validação básica
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Tentativa de login
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Mensagem de sucesso para SweetAlert
            return redirect()->intended(route('admin.dashboard'))
                             ->with('success', 'Bem-vindo(a) à área administrativa!');
        }

        // Mensagem genérica para qualquer falha
        return back()->withErrors(['login' => 'Usuário ou senha incorretos.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
