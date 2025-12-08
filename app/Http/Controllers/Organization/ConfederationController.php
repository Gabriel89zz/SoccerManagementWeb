<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Confederation;
use Illuminate\Http\Request;

class ConfederationController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = Confederation::query();

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre (UEFA) o acrónimo (UEFA)
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('acronym', 'like', '%' . $search . '%');
            });
        }

        $confederations = $query->orderBy('name')->paginate(10);
        $confederations->appends(['search' => $request->search]);

        return view('organization.confederations.index', compact('confederations'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('organization.confederations.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:confederation,name',
            'acronym' => 'nullable|max:10|unique:confederation,acronym',
            'foundation_year' => 'nullable|integer|min:1800|max:'.date('Y'),
        ]);

        Confederation::create($validated);
        return redirect()->route('confederations.index')->with('success', 'Confederation created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $confederation = Confederation::findOrFail($id);
        return view('organization.confederations.show', compact('confederation'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        $confederation = Confederation::findOrFail($id);
        return view('organization.confederations.edit', compact('confederation'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Validamos unique ignorando el ID actual
            'name' => 'required|max:100|unique:confederation,name,'.$id.',confederation_id',
            'acronym' => 'nullable|max:10|unique:confederation,acronym,'.$id.',confederation_id',
            'foundation_year' => 'nullable|integer|min:1800|max:'.date('Y'),
        ]);

        $confederation = Confederation::findOrFail($id);
        $confederation->update($validated);

        return redirect()->route('confederations.index')->with('success', 'Confederation updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $confederation = Confederation::findOrFail($id);
        $confederation->delete();
        return redirect()->route('confederations.index')->with('success', 'Confederation deleted successfully.');
    }
}