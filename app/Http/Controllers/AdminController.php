<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Processar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // DEBUG: Log da tentativa
        \Log::info('Tentativa de login', [
            'email' => $credentials['email'],
            'remember' => $request->boolean('remember')
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // DEBUG: Login bem-sucedido
            \Log::info('Login bem-sucedido', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email
            ]);
            
            return redirect()->intended(route('admin.dashboard'));
        }

        // DEBUG: Login falhou
        \Log::warning('Login falhou', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }

    /**
     * Dashboard admin - página de upload
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
