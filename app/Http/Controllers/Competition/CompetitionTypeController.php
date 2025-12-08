<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\CompetitionType;
use Illuminate\Http\Request;

class CompetitionTypeController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = CompetitionType::query();

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            // Búsqueda por type_name
            $query->where('type_name', 'like', '%' . $search . '%');
        }

        // Ordenamos por type_name
        $types = $query->orderBy('type_name')->paginate(10);
        $types->appends(['search' => $request->search]);

        return view('competition.competition_types.index', compact('types'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('competition.competition_types.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            // La columna es 'type_name'
            'type_name' => 'required|max:100|unique:competition_type,type_name',
        ]);

        CompetitionType::create($validated);
        return redirect()->route('competition-types.index')->with('success', 'Competition Type created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $type = CompetitionType::findOrFail($id);
        return view('competition.competition_types.show', compact('type'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $type = CompetitionType::findOrFail($id);
        return view('competition.competition_types.edit', compact('type'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Validamos unique ignorando el ID actual (type_id)
            'type_name' => 'required|max:100|unique:competition_type,type_name,'.$id.',type_id',
        ]);

        $type = CompetitionType::findOrFail($id);
        $type->update($validated);

        return redirect()->route('competition-types.index')->with('success', 'Competition Type updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $type = CompetitionType::findOrFail($id);
        $type->delete();
        return redirect()->route('competition-types.index')->with('success', 'Competition Type deleted successfully.');
    }
}