<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Stadium;
use App\Models\Core\City;
use Illuminate\Http\Request;
// Importamos las clases para la paginación manual
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class StadiumController extends Controller
{
    public function index(Request $request)
    {
        $query = Stadium::with('city');

        // BÚSQUEDA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('capacity', 'like', '%' . $search . '%')
                  ->orWhereHas('city', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        $allStadiums = $query->orderBy('name')->get();

        $uniqueStadiums = $allStadiums->unique('name');

        $currentPage = Paginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $uniqueStadiums->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $stadiums = new LengthAwarePaginator(
            $currentItems,
            $uniqueStadiums->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        // Mantenemos la búsqueda en los links
        $stadiums->appends(['search' => $request->search]);

        return view('organization.stadiums.index', compact('stadiums'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // CORRECCIÓN: Ya no cargamos 150k ciudades. La vista usará Ajax.
        return view('organization.stadiums.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'capacity' => 'nullable|integer|min:0',
            'city_id' => 'required|exists:city,city_id',
        ]);

        Stadium::create($validated);
        return redirect()->route('stadiums.index')->with('success', 'Stadium created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargamos city y el country de esa city para mostrar la ubicación completa
        $stadium = Stadium::with('city.country')->findOrFail($id);
        return view('organization.stadiums.show', compact('stadium'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // Cargamos el estadio CON su ciudad y el país de la ciudad para mostrar el valor actual
        $stadium = Stadium::with('city.country')->findOrFail($id);
        
        // CORRECCIÓN: No enviamos $cities.
        return view('organization.stadiums.edit', compact('stadium'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'capacity' => 'nullable|integer|min:0',
            'city_id' => 'required|exists:city,city_id',
        ]);

        $stadium = Stadium::findOrFail($id);
        $stadium->update($validated);

        return redirect()->route('stadiums.index')->with('success', 'Stadium updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $stadium = Stadium::findOrFail($id);
        $stadium->delete();
        return redirect()->route('stadiums.index')->with('success', 'Stadium deleted successfully.');
    }
}