<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        // SOLO ADMINS PUEDEN VER ESTO
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Access Denied.');
        }

        $query = User::query();

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $users->appends(['search' => $request->search]);

        return view('core.users.index', compact('users'));
    }

    // CREATE FORM
    public function create()
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');
        return view('core.users.create');
    }

    // STORE
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');

        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'username' => 'required|max:50|unique:user,username',
            'email' => 'required|email|max:100|unique:user,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:Administrador,Usuario',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, // El modelo lo hashea
            'role' => $request->role,
            'is_active' => 1,
            'is_staff' => ($request->role === 'Administrador'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // SHOW DETAIL (AUDITORÍA)
    public function show($id)
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');

        // Cargar quien creó/editó al usuario (Self Join)
        // Necesitamos definir estas relaciones en el Modelo User primero (ver abajo)
        $user = User::findOrFail($id);

        return view('core.users.show', compact('user'));
    }

    // EDIT FORM
    public function edit($id)
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');
        $user = User::findOrFail($id);
        return view('core.users.edit', compact('user'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'username' => 'required|max:50|unique:user,username,' . $id . ',user_id',
            'email' => 'required|email|max:100|unique:user,email,' . $id . ',user_id',
            'role' => 'required|in:Administrador,Usuario',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->is_staff = ($request->role === 'Administrador');

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        if (!Auth::user()->isAdmin())
            return redirect()->route('dashboard');

        $user = User::findOrFail($id);

        // Evitar auto-borrado
        if ($user->user_id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete(); // Soft Delete
        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }
}