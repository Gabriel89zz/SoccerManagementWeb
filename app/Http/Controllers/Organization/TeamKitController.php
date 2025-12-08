<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\TeamKit;
use App\Models\Organization\Team;
use Illuminate\Http\Request;

class TeamKitController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading
        $query = TeamKit::with('team');

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por tipo de kit (Home, Away, etc.)
                $q->where('kit_type', 'like', '%' . $search . '%')
                  // O por nombre del equipo
                  ->orWhereHas('team', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $teamKits = $query->orderBy('kit_id')->paginate(10);
        $teamKits->appends(['search' => $request->search]);

        return view('organization.team_kits.index', compact('teamKits'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar equipos activos y únicos
        $teams = Team::where('is_active', 1)
                     ->orderBy('name')
                     ->get()
                     ->unique('name');
        
        return view('organization.team_kits.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'kit_type' => 'required|max:100', // Ej: Home 23/24
        ]);

        TeamKit::create($validated);
        return redirect()->route('team-kits.index')->with('success', 'Team Kit created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargar la relación 'team' para mostrar el nombre del equipo
        $teamKit = TeamKit::with('team')->findOrFail($id);
        return view('organization.team_kits.show', compact('teamKit'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // PK es 'kit_id'
        $teamKit = TeamKit::findOrFail($id);
        
        $teams = Team::where('is_active', 1)
                     ->orderBy('name')
                     ->get()
                     ->unique('name');

        return view('organization.team_kits.edit', compact('teamKit', 'teams'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'kit_type' => 'required|max:100',
        ]);

        $teamKit = TeamKit::findOrFail($id);
        $teamKit->update($validated);

        return redirect()->route('team-kits.index')->with('success', 'Team Kit updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $teamKit = TeamKit::findOrFail($id);
        $teamKit->delete();
        return redirect()->route('team-kits.index')->with('success', 'Team Kit deleted successfully.');
    }
}