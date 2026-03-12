<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLogin() {
        return Inertia::render('Auth/Login');
    }

    public function showRegister() {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // Validação de 6 chars e confirmação
        ]);

        User::create($data);
        return redirect()->route('login')->with('success', 'Cadastro realizado!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tentamos o login com o adicional do is_active
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true], $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas ou conta bloqueada.',
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Redireciona para o Dashboard
            return redirect()->intended('/dashboard');
        }

        // Se falhar, retorna com erro de validação
        return back()->withErrors([
            'email' => 'As credenciais não coincidem com nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        // Importante para limpar a sessão no banco e evitar o erro 419 ao tentar relogar
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}