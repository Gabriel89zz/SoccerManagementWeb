<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = Formation::query();

        // BÚSQUEDA AJAX EN SERVIDOR
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $formations = $query->orderBy('name')->paginate(15);
        $formations->appends(['search' => $request->search]);

        return view('core.formations.index', compact('formations'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.formations.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:formation,name',
        ]);

        Formation::create($validated);
        return redirect()->route('formations.index')->with('success', 'Formation created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $formation = Formation::findOrFail($id);
        return view('core.formations.show', compact('formation'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $formation = Formation::findOrFail($id);
        return view('core.formations.edit', compact('formation'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:formation,name,' . $id . ',formation_id',
        ]);

        $formation = Formation::findOrFail($id);
        $formation->update($validated);

        return redirect()->route('formations.index')->with('success', 'Formation updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $formation = Formation::findOrFail($id);
        $formation->delete();
        return redirect()->route('formations.index')->with('success', 'Formation deleted successfully.');
    }
}