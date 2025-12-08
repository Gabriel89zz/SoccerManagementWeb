<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\Competition;
use App\Models\Competition\CompetitionType;
use App\Models\Core\Country;
use App\Models\Organization\Confederation;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos Tipo, País y Confederación
        $query = Competition::with(['type', 'country', 'confederation']); // CAMBIA 'name' por 'type'

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre
                $q->where('name', 'like', '%' . $search . '%')
                  // Buscar por Tipo
                  ->orWhereHas('type', function($sq) use ($search) {
                      $sq->where('type_name', 'like', '%' . $search . '%'); // Usa 'type_name' si ese es el campo
                  })
                  // Buscar por País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por Confederación
                  ->orWhereHas('confederation', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $competitions = $query->orderBy('name')->paginate(10);
        $competitions->appends(['search' => $request->search]);

        return view('competition.competitions.index', compact('competitions'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // CORRIGE AQUÍ TAMBIÉN: usa 'type_name' en lugar de 'name' para orderBy
        $types = CompetitionType::where('is_active', 1)->orderBy('type_name')->get();
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $confederations = Confederation::where('is_active', 1)->orderBy('name')->get();
        
        return view('competition.competitions.create', compact('types', 'countries', 'confederations'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:150|unique:competition,name',
            'competition_type_id' => 'required|exists:competition_type,type_id', // CAMBIA: competition_type_id → type_id
            'country_id' => 'nullable|exists:country,country_id',
            'confederation_id' => 'nullable|exists:confederation,confederation_id',
        ]);

        Competition::create($validated);
        return redirect()->route('competitions.index')->with('success', 'Competition created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $competition = Competition::with(['type', 'country', 'confederation'])->findOrFail($id);
        return view('competition.competitions.show', compact('competition'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $competition = Competition::findOrFail($id);
        
        // CORRIGE AQUÍ TAMBIÉN: usa 'type_name' en lugar de 'name' para orderBy
        $types = CompetitionType::where('is_active', 1)->orderBy('type_name')->get();
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $confederations = Confederation::where('is_active', 1)->orderBy('name')->get();

        return view('competition.competitions.edit', compact('competition', 'types', 'countries', 'confederations'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:150|unique:competition,name,'.$id.',competition_id',
            'competition_type_id' => 'required|exists:competition_type,type_id', // CAMBIA: competition_type_id → type_id
            'country_id' => 'nullable|exists:country,country_id',
            'confederation_id' => 'nullable|exists:confederation,confederation_id',
        ]);

        $competition = Competition::findOrFail($id);
        $competition->update($validated);

        return redirect()->route('competitions.index')->with('success', 'Competition updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        $competition->delete();
        return redirect()->route('competitions.index')->with('success', 'Competition deleted successfully.');
    }
}