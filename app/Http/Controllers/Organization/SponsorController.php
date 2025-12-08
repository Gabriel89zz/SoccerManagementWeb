<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Sponsor;
use App\Models\Core\Country;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading para optimizar la carga del país
        $query = Sponsor::with('country');

        // LÓGICA DE BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del patrocinador
                $q->where('name', 'like', '%' . $search . '%')
                  // O por industria (ej: Technology, Airline)
                  ->orWhere('industry', 'like', '%' . $search . '%')
                  // O por nombre del país
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $sponsors = $query->orderBy('name')->paginate(10);
        $sponsors->appends(['search' => $request->search]);

        return view('organization.sponsors.index', compact('sponsors'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar países activos y únicos para el Select2
        $countries = Country::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name');
        
        return view('organization.sponsors.create', compact('countries'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'industry' => 'nullable|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        Sponsor::create($validated);
        return redirect()->route('sponsors.index')->with('success', 'Sponsor created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $sponsor = Sponsor::with('country')->findOrFail($id);
        return view('organization.sponsors.show', compact('sponsor'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        
        $countries = Country::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name');

        return view('organization.sponsors.edit', compact('sponsor', 'countries'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'industry' => 'nullable|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        $sponsor = Sponsor::findOrFail($id);
        $sponsor->update($validated);

        return redirect()->route('sponsors.index')->with('success', 'Sponsor updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->delete();
        return redirect()->route('sponsors.index')->with('success', 'Sponsor deleted successfully.');
    }
}