<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\SquadStaff;
use App\Models\Management\Squad;
use App\Models\People\StaffMember;
use Illuminate\Http\Request;

class SquadStaffController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Plantilla (Equipo/Temporada), Staff (con Rol)
        $query = SquadStaff::with(['squad.team', 'squad.season', 'staffMember.role']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del staff
                $q->whereHas('staffMember', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por nombre del rol (Entrenador, Fisio, etc)
                ->orWhereHas('staffMember.role', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del equipo
                ->orWhereHas('squad.team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $staffAssignments = $query->orderBy('squad_staff_id', 'desc')->paginate(10);
        $staffAssignments->appends(['search' => $request->search]);

        return view('management.squad_staff.index', compact('staffAssignments'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar plantillas activas con contexto
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        // Cargar miembros del staff con su rol
        $staffMembers = StaffMember::with('role')
                                   ->where('is_active', 1)
                                   ->orderBy('last_name')
                                   ->get();
        
        return view('management.squad_staff.create', compact('squads', 'staffMembers'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'staff_member_id' => 'required|exists:staff_member,staff_member_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Validar duplicados: El mismo staff no debería estar dos veces en la misma plantilla en el mismo periodo (lógica simple por ahora)
        $exists = SquadStaff::where('squad_id', $request->squad_id)
                            ->where('staff_member_id', $request->staff_member_id)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This staff member is already assigned to this squad.'])->withInput();
        }

        SquadStaff::create($validated);
        return redirect()->route('squad-staff.index')->with('success', 'Staff member assigned to squad successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $squadStaff = SquadStaff::with(['squad.team', 'squad.season', 'staffMember.role'])->findOrFail($id);
        return view('management.squad_staff.show', compact('squadStaff'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $squadStaff = SquadStaff::findOrFail($id);
        
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        $staffMembers = StaffMember::with('role')
                                   ->where('is_active', 1)
                                   ->orderBy('last_name')
                                   ->get();

        return view('management.squad_staff.edit', compact('squadStaff', 'squads', 'staffMembers'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'staff_member_id' => 'required|exists:staff_member,staff_member_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Validar duplicados excluyendo actual
        $exists = SquadStaff::where('squad_id', $request->squad_id)
                            ->where('staff_member_id', $request->staff_member_id)
                            ->where('squad_staff_id', '!=', $id)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This staff member is already assigned to this squad.'])->withInput();
        }

        $squadStaff = SquadStaff::findOrFail($id);
        $squadStaff->update($validated);

        return redirect()->route('squad-staff.index')->with('success', 'Assignment updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $squadStaff = SquadStaff::findOrFail($id);
        $squadStaff->delete();
        return redirect()->route('squad-staff.index')->with('success', 'Staff member removed from squad successfully.');
    }
}