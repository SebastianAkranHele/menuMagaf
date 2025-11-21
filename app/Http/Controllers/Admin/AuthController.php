<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Impedir acesso direto sem código
        //if (!Session::get('admin_access_granted')) {
            //abort(403, 'Acesso não autorizado.');
        //}

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // 🔒 Esquecer o acesso após login
            Session::forget('admin_access_granted');

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Bem-vindo(a) à área administrativa!');
        }

        return back()->withErrors(['login' => 'Usuário ou senha incorretos.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Redireciona para a home em vez do login
        return redirect('/')
            ->with('success', 'Sessão encerrada com sucesso.');
    }
}
