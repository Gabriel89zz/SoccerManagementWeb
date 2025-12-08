<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // 1. Mostrar el formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // 2. Guardar el nuevo usuario
    public function register(Request $request)
    {
        // Validar datos
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:user', // Tabla 'user'
            'password' => 'required|string|min:8|confirmed', // Confirmed busca field 'password_confirmation'
        ]);

        // Crear usuario
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => explode('@', $request->email)[0] . rand(100, 999), // Username automático temporal
            'password' => $request->password, // El modelo User lo hashea automáticamente en 'casts'
            'role' => 'Usuario', // Rol por defecto
            'is_active' => 1,
            'is_staff' => 0,
            'is_superuser' => 0,
            'created_at' => now(),
        ]);

        // Loguear automáticamente al usuario
        Auth::login($user);

        // CORRECCIÓN: Redirigir al dashboard (Antes decía 'home')
        return redirect()->route('dashboard');
    }
}