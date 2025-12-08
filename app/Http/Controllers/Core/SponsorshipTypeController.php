<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\SponsorshipType;
use Illuminate\Http\Request;

class SponsorshipTypeController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = SponsorshipType::query();

        // BÚSQUEDA AJAX EN SERVIDOR
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('type_name', 'like', '%' . $search . '%');
        }

        $types = $query->orderBy('type_name')->paginate(15);
        
        // Mantener la búsqueda en la paginación
        $types->appends(['search' => $request->search]);

        return view('core.sponsorship_types.index', compact('types'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.sponsorship_types.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Campo es 'type_name'
            'type_name' => 'required|max:100|unique:sponsorship_type,type_name',
        ]);

        SponsorshipType::create($validated);
        return redirect()->route('sponsorship-types.index')->with('success', 'Sponsorship Type created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $type = SponsorshipType::findOrFail($id);
        return view('core.sponsorship_types.show', compact('type'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $type = SponsorshipType::findOrFail($id);
        return view('core.sponsorship_types.edit', compact('type'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type_name' => 'required|max:100|unique:sponsorship_type,type_name,'.$id.',sponsorship_type_id',
        ]);

        $type = SponsorshipType::findOrFail($id);
        $type->update($validated);

        return redirect()->route('sponsorship-types.index')->with('success', 'Sponsorship Type updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $type = SponsorshipType::findOrFail($id);
        $type->delete();
        return redirect()->route('sponsorship-types.index')->with('success', 'Sponsorship Type deleted successfully.');
    }
}