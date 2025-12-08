<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Coach;
use App\Models\Core\Country;
use Illuminate\Http\Request;
// Importamos las clases para paginación manual
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class CoachController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica y Filtro de Duplicados)
    public function index(Request $request)
    {
        // Eager Loading del País
        $query = Coach::with('country');

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre o Apellido
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  // Nivel de Licencia
                  ->orWhere('license_level', 'like', '%' . $search . '%')
                  // País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 1. Obtenemos TODOS los resultados ordenados
        $allCoaches = $query->orderBy('last_name')->get();

        // 2. Filtramos los duplicados usando una combinación de Nombre + Apellido
        // Esto asegura que visualmente no tengas a la misma persona dos veces
        $uniqueCoaches = $allCoaches->unique(function ($item) {
            return $item->first_name . $item->last_name;
        });

        // 3. Implementamos Paginación Manual sobre la colección única
        $currentPage = Paginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $uniqueCoaches->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $coaches = new LengthAwarePaginator(
            $currentItems,
            $uniqueCoaches->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        // Mantenemos el parámetro de búsqueda en los enlaces de paginación
        $coaches->appends(['search' => $request->search]);

        return view('people.coaches.index', compact('coaches'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        return view('people.coaches.create', compact('countries'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'license_level' => 'required|max:50', // Ej: UEFA Pro
        ]);

        Coach::create($validated);
        return redirect()->route('coaches.index')->with('success', 'Coach created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $coach = Coach::with('country')->findOrFail($id);
        return view('people.coaches.show', compact('coach'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $coach = Coach::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();

        return view('people.coaches.edit', compact('coach', 'countries'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'license_level' => 'required|max:50',
        ]);

        $coach = Coach::findOrFail($id);
        $coach->update($validated);

        return redirect()->route('coaches.index')->with('success', 'Coach updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $coach = Coach::findOrFail($id);
        $coach->delete();
        return redirect()->route('coaches.index')->with('success', 'Coach deleted successfully.');
    }
}