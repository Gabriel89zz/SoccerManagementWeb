<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\KitSponsor;
use App\Models\Organization\Sponsor;
use App\Models\Organization\TeamKit;
use Illuminate\Http\Request;

class KitSponsorController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading profundo: Traemos Sponsor y el Kit con su Equipo
        $query = KitSponsor::with(['sponsor', 'kit.team']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placement', 'like', '%' . $search . '%')
                  // Buscar por nombre del patrocinador
                  ->orWhereHas('sponsor', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // Buscar por equipo dueño del uniforme
                  ->orWhereHas('kit.team', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $kitSponsors = $query->orderBy('kit_id')->paginate(10);
        $kitSponsors->appends(['search' => $request->search]);

        return view('organization.kit_sponsors.index', compact('kitSponsors'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $sponsors = Sponsor::where('is_active', 1)->orderBy('name')->get();
        // Traemos los kits con su equipo para mostrar "Equipo - Tipo de Kit"
        $kits = TeamKit::with('team')->where('is_active', 1)->get();
        
        return view('organization.kit_sponsors.create', compact('sponsors', 'kits'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sponsor_id' => 'required|exists:sponsor,sponsor_id',
            'kit_id' => 'required|exists:team_kit,kit_id',
            'placement' => 'nullable|max:50', // Chest, Sleeve, Back
            'is_primary' => 'required|boolean',
        ]);

        KitSponsor::create($validated);
        return redirect()->route('kit-sponsors.index')->with('success', 'Kit Sponsor assigned successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargar relaciones profundas para mostrar nombre del sponsor y del equipo
        $kitSponsor = KitSponsor::with(['sponsor', 'kit.team'])->findOrFail($id);
        return view('organization.kit_sponsors.show', compact('kitSponsor'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // La PK es 'id' según tu modelo KitSponsor
        $kitSponsor = KitSponsor::findOrFail($id);
        
        $sponsors = Sponsor::where('is_active', 1)->orderBy('name')->get();
        $kits = TeamKit::with('team')->where('is_active', 1)->get();

        return view('organization.kit_sponsors.edit', compact('kitSponsor', 'sponsors', 'kits'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sponsor_id' => 'required|exists:sponsor,sponsor_id',
            'kit_id' => 'required|exists:team_kit,kit_id',
            'placement' => 'nullable|max:50',
            'is_primary' => 'required|boolean',
        ]);

        $kitSponsor = KitSponsor::findOrFail($id);
        $kitSponsor->update($validated);

        return redirect()->route('kit-sponsors.index')->with('success', 'Kit Sponsor updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $kitSponsor = KitSponsor::findOrFail($id);
        $kitSponsor->delete();
        return redirect()->route('kit-sponsors.index')->with('success', 'Kit Sponsor deleted successfully.');
    }
}