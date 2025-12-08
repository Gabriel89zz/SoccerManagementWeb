<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Agency;
use App\Models\Core\Country;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading
        $query = Agency::with('country');

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre de agencia
                $q->where('name', 'like', '%' . $search . '%')
                  // O por nombre del país
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $agencies = $query->orderBy('name')->paginate(10);
        $agencies->appends(['search' => $request->search]);

        return view('organization.agencies.index', compact('agencies'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Carga estándar para Países (pocos registros), filtrando duplicados
        $countries = Country::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name');
        
        return view('organization.agencies.create', compact('countries'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        Agency::create($validated);
        return redirect()->route('agencies.index')->with('success', 'Agency created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargamos la relación country para mostrar el nombre del país
        $agency = Agency::with('country')->findOrFail($id);
        return view('organization.agencies.show', compact('agency'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        
        $countries = Country::where('is_active', 1)
                            ->orderBy('name')
                            ->get()
                            ->unique('name');

        return view('organization.agencies.edit', compact('agency', 'countries'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        $agency = Agency::findOrFail($id);
        $agency->update($validated);

        return redirect()->route('agencies.index')->with('success', 'Agency updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();
        return redirect()->route('agencies.index')->with('success', 'Agency deleted successfully.');
    }
}