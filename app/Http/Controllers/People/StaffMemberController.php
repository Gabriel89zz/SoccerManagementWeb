<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\StaffMember;
use App\Models\Core\Country;
use App\Models\Core\StaffRole;
use Illuminate\Http\Request;

class StaffMemberController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos País y Rol
        $query = StaffMember::with(['country', 'role']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre o Apellido
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  // Buscar por nombre del País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por nombre del Rol
                  ->orWhereHas('role', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $staffMembers = $query->orderBy('last_name')->paginate(10);
        $staffMembers->appends(['search' => $request->search]);

        return view('people.staff_members.index', compact('staffMembers'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $roles = StaffRole::where('is_active', 1)->orderBy('name')->get();
        
        return view('people.staff_members.create', compact('countries', 'roles'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'role_id' => 'required|exists:staff_role,role_id',
        ]);

        StaffMember::create($validated);
        return redirect()->route('staff-members.index')->with('success', 'Staff Member created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $staffMember = StaffMember::with(['country', 'role'])->findOrFail($id);
        return view('people.staff_members.show', compact('staffMember'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $staffMember = StaffMember::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        //$roles = StaffRole::where('is_active', 1)->orderBy('name')->get();

        return view('people.staff_members.edit', compact('staffMember', 'countries'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'role_id' => 'required|exists:staff_role,role_id',
        ]);

        $staffMember = StaffMember::findOrFail($id);
        $staffMember->update($validated);

        return redirect()->route('staff-members.index')->with('success', 'Staff Member updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $staffMember = StaffMember::findOrFail($id);
        $staffMember->delete();
        return redirect()->route('staff-members.index')->with('success', 'Staff Member deleted successfully.');
    }
}