<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{
    /**
     * Show the client login form
     */
    public function showLoginForm()
    {
        // Se já está logado e é cliente, vai pro painel
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->hasRole('cliente')) {
            return redirect()->route('cliente.dashboard');
        }

        return view('client.auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            if (! $user->hasRole('cliente')) {
                // Se um admin tentar usar essa porta, barra.
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Acesso negado. Apenas clientes podem acessar este portal.',
                ])->onlyInput('email');
            }

            return redirect()->route('cliente.dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cliente.login');
    }
}
