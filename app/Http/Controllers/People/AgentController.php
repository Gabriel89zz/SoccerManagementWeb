<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Agent;
use App\Models\Core\Country;
use App\Models\Organization\Agency;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos País y Agencia
        $query = Agent::with(['country', 'agency']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Nombre, Apellido o Licencia
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('license_number', 'like', '%' . $search . '%')
                  // Buscar por nombre del País
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por nombre de la Agencia
                  ->orWhereHas('agency', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $agents = $query->orderBy('last_name')->paginate(10);
        $agents->appends(['search' => $request->search]);

        return view('people.agents.index', compact('agents'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $agencies = Agency::where('is_active', 1)->orderBy('name')->get();
        
        return view('people.agents.create', compact('countries', 'agencies'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'license_number' => 'nullable|max:50',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'agency_id' => 'nullable|exists:agency,agency_id',
        ]);

        Agent::create($validated);
        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $agent = Agent::with(['country', 'agency'])->findOrFail($id);
        return view('people.agents.show', compact('agent'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $agencies = Agency::where('is_active', 1)->orderBy('name')->get();

        return view('people.agents.edit', compact('agent', 'countries', 'agencies'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'license_number' => 'nullable|max:50',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:country,country_id',
            'agency_id' => 'nullable|exists:agency,agency_id',
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update($validated);

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();
        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
    }
}