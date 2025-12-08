<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Referee;
use App\Models\Core\Country;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        $query = Referee::with('country');

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre o Apellido
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  // Nivel de Certificación
                  ->orWhere('certification_level', 'like', '%' . $search . '%')
                  // País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $referees = $query->orderBy('last_name')->paginate(10);
        $referees->appends(['search' => $request->search]);

        return view('people.referees.index', compact('referees'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        return view('people.referees.create', compact('countries'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'certification_level' => 'required|max:50', // Ej: FIFA, National
        ]);

        Referee::create($validated);
        return redirect()->route('referees.index')->with('success', 'Referee created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $referee = Referee::with('country')->findOrFail($id);
        return view('people.referees.show', compact('referee'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $referee = Referee::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();

        return view('people.referees.edit', compact('referee', 'countries'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'certification_level' => 'required|max:50',
        ]);

        $referee = Referee::findOrFail($id);
        $referee->update($validated);

        return redirect()->route('referees.index')->with('success', 'Referee updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $referee = Referee::findOrFail($id);
        $referee->delete();
        return redirect()->route('referees.index')->with('success', 'Referee deleted successfully.');
    }
}