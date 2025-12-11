<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\StaffRole;
use Illuminate\Http\Request;

class StaffRoleController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = StaffRole::query();

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            // Buscar por nombre del rol
            $query->where('role_name', 'like', '%' . $search . '%');
        }

        // Ordenamos por nombre
        $roles = $query->orderBy('role_name')->paginate(10);
        $roles->appends(['search' => $request->search]);

        return view('core.staff_roles.index', compact('roles'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('core.staff_roles.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|max:100|unique:staff_role,role_name',
        ]);

        StaffRole::create($validated);
        return redirect()->route('staff-roles.index')->with('success', 'Staff Role created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $role = StaffRole::findOrFail($id);
        return view('core.staff_roles.show', compact('role'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $role = StaffRole::findOrFail($id);
        return view('core.staff_roles.edit', compact('role'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'role_name' => 'required|max:100|unique:staff_role,role_name,' . $id . ',role_id',
        ]);

        $role = StaffRole::findOrFail($id);
        $role->update($validated);

        return redirect()->route('staff-roles.index')->with('success', 'Staff Role updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $role = StaffRole::findOrFail($id);
        $role->delete();
        return redirect()->route('staff-roles.index')->with('success', 'Staff Role deleted successfully.');
    }
}