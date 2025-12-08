<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Mostrar el formulario de Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Procesar el Login
    public function login(Request $request)
    {
        // Validar datos del formulario
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar (Email + Password + is_active = 1)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1])) {
            
            // Si es correcto, regenerar sesión por seguridad
            $request->session()->regenerate();

            // Redirigir al dashboard (o a donde intentaba ir)
            return redirect()->intended('dashboard');
        }

        // Si falla, regresar con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden o el usuario está inactivo.',
        ])->onlyInput('email');
    }

    // 3. Cerrar Sesión (Logout)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}