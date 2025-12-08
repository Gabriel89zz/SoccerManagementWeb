<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Scout;
use App\Models\Core\Country;
use App\Models\Organization\Team;
use Illuminate\Http\Request;

class ScoutController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos País y Equipo Empleador
        $query = Scout::with(['country', 'employingTeam']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre, Apellido o Región
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('region', 'like', '%' . $search . '%')
                  // Buscar por nombre del País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por nombre del Equipo
                  ->orWhereHas('employingTeam', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $scouts = $query->orderBy('last_name')->paginate(10);
        $scouts->appends(['search' => $request->search]);

        return view('people.scouts.index', compact('scouts'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        // Cargamos equipos para asignar quién emplea al scout
        $teams = Team::where('is_active', 1)->orderBy('name')->get()->unique('name');
        
        return view('people.scouts.create', compact('countries', 'teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'region' => 'nullable|max:100', // Ej: South America
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'employing_team_id' => 'nullable|exists:team,team_id',
        ]);

        Scout::create($validated);
        return redirect()->route('scouts.index')->with('success', 'Scout created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $scout = Scout::with(['country', 'employingTeam'])->findOrFail($id);
        return view('people.scouts.show', compact('scout'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $scout = Scout::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $teams = Team::where('is_active', 1)->orderBy('name')->get()->unique('name');

        return view('people.scouts.edit', compact('scout', 'countries', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'region' => 'nullable|max:100',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'employing_team_id' => 'nullable|exists:team,team_id',
        ]);

        $scout = Scout::findOrFail($id);
        $scout->update($validated);

        return redirect()->route('scouts.index')->with('success', 'Scout updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $scout = Scout::findOrFail($id);
        $scout->delete();
        return redirect()->route('scouts.index')->with('success', 'Scout deleted successfully.');
    }
}