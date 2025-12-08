<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Academy;
use App\Models\Organization\Team;
// use App\Models\Core\City; // NO cargamos el modelo City masivamente
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = Academy::with(['city', 'team']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('team', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $academies = $query->orderBy('name')->paginate(10);
        $academies->appends(['search' => $request->search]);

        return view('organization.academies.index', compact('academies'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Equipos: Cargamos normal (son pocos) y filtramos duplicados
        $teams = Team::where('is_active', 1)
                     ->orderBy('name')
                     ->get()
                     ->unique('name');

        // Ciudades: NO cargamos nada. Se usa AJAX en la vista.
        return view('organization.academies.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'team_id' => 'required|exists:team,team_id',
            'city_id' => 'required|exists:city,city_id',
        ]);

        Academy::create($validated);
        return redirect()->route('academies.index')->with('success', 'Academy created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargar relaciones para mostrar detalles completos: Ciudad con País y Equipo
        $academy = Academy::with(['city.country', 'team'])->findOrFail($id);
        return view('organization.academies.show', compact('academy'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // Cargamos la academia con sus relaciones
        $academy = Academy::with(['city.country', 'team'])->findOrFail($id);

        $teams = Team::where('is_active', 1)
                      ->orderBy('name')
                      ->get()
                      ->unique('name');

        // Ciudades: AJAX en la vista
        return view('organization.academies.edit', compact('academy', 'teams'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'team_id' => 'required|exists:team,team_id',
            'city_id' => 'required|exists:city,city_id',
        ]);

        $academy = Academy::findOrFail($id);
        $academy->update($validated);

        return redirect()->route('academies.index')->with('success', 'Academy updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $academy = Academy::findOrFail($id);
        $academy->delete();
        return redirect()->route('academies.index')->with('success', 'Academy deleted successfully.');
    }
}