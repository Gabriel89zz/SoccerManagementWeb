<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Team;
use App\Models\Core\Country;
use App\Models\Organization\Stadium;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // 1. LIST TEAMS (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Iniciamos la query con las relaciones para optimizar (Eager Loading)
        $query = Team::with(['country', 'stadium']);

        // LÓGICA DE BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_name', 'like', '%' . $search . '%')
                  // Buscar por nombre del País relacionado
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por nombre del Estadio relacionado
                  ->orWhereHas('stadium', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Ordenamos y paginamos
        $teams = $query->orderBy('name')->paginate(10);
        
        // Mantenemos el parámetro de búsqueda en la paginación
        $teams->appends(['search' => $request->search]);

        return view('organization.teams.index', compact('teams'));
    }

    // 2. SHOW CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        
        // Filtrar estadios para que sean únicos por nombre visualmente
        $stadiums = Stadium::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name'); 
        
        return view('organization.teams.create', compact('countries', 'stadiums'));
    }

    // 3. STORE NEW TEAM
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'short_name' => 'required|max:50',
            'country_id' => 'required|exists:country,country_id',
            'home_stadium_id' => 'nullable|exists:stadium,stadium_id',
            'foundation_date' => 'nullable|date',
        ]);

        Team::create($validated);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargamos relaciones para mostrar nombres en lugar de IDs
        $team = Team::with(['country', 'stadium'])->findOrFail($id);
        return view('organization.teams.show', compact('team'));
    }

    // 4. SHOW EDIT FORM
    public function edit($id)
    {
        $team = Team::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();

        // Filtrar estadios únicos aquí también
        $stadiums = Stadium::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name');

        return view('organization.teams.edit', compact('team', 'countries', 'stadiums'));
    }

    // 5. UPDATE TEAM
    public function update(Request $request, $id)
    {
        // No validamos ni permitimos actualizar 'is_active' aquí.
        $validated = $request->validate([
            'name' => 'required|max:150',
            'short_name' => 'required|max:50',
            'country_id' => 'required|exists:country,country_id',
            'home_stadium_id' => 'nullable|exists:stadium,stadium_id',
            'foundation_date' => 'nullable|date',
        ]);

        $team = Team::findOrFail($id);
        $team->update($validated);

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    // 6. DELETE TEAM (Soft Delete)
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete(); 

        return redirect()->route('teams.index')->with('success', 'Team deleted (deactivated) successfully.');
    }
}