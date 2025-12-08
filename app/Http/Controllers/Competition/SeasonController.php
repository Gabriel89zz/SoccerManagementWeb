<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = Season::query();

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            // Buscar por nombre (ej: "2023-2024")
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Ordenamos por nombre descendente (lo más común para temporadas: 2024, 2023...)
        $seasons = $query->orderBy('name', 'desc')->paginate(10);
        $seasons->appends(['search' => $request->search]);

        return view('competition.seasons.index', compact('seasons'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('competition.seasons.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:season,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Season::create($validated);
        return redirect()->route('seasons.index')->with('success', 'Season created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $season = Season::findOrFail($id);
        return view('competition.seasons.show', compact('season'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $season = Season::findOrFail($id);
        return view('competition.seasons.edit', compact('season'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Validamos unique ignorando el ID actual (season_id)
            'name' => 'required|max:100|unique:season,name,'.$id.',season_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $season = Season::findOrFail($id);
        $season->update($validated);

        return redirect()->route('seasons.index')->with('success', 'Season updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $season = Season::findOrFail($id);
        $season->delete();
        return redirect()->route('seasons.index')->with('success', 'Season deleted successfully.');
    }
}