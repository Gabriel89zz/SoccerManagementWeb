<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\InjuryType;
use Illuminate\Http\Request;

class InjuryTypeController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = InjuryType::query();

        // BÚSQUEDA AJAX EN SERVIDOR
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('severity_level', 'like', '%' . $search . '%');
            });
        }

        $injuryTypes = $query->orderBy('name')->paginate(15);
        $injuryTypes->appends(['search' => $request->search]);

        return view('core.injury_types.index', compact('injuryTypes'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.injury_types.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:injury_type,name',
            'severity_level' => 'required|max:20',
        ]);

        InjuryType::create($validated);
        return redirect()->route('injury-types.index')->with('success', 'Injury Type created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $injuryType = InjuryType::findOrFail($id);
        return view('core.injury_types.show', compact('injuryType'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $injuryType = InjuryType::findOrFail($id);
        return view('core.injury_types.edit', compact('injuryType'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:injury_type,name,' . $id . ',injury_type_id',
            'severity_level' => 'required|max:20',
        ]);

        $injuryType = InjuryType::findOrFail($id);
        $injuryType->update($validated);

        return redirect()->route('injury-types.index')->with('success', 'Injury Type updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $injuryType = InjuryType::findOrFail($id);
        $injuryType->delete();
        return redirect()->route('injury-types.index')->with('success', 'Injury Type deleted successfully.');
    }
}