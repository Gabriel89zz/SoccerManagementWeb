<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\TeamSponsorship;
use App\Models\Organization\Team;
use App\Models\Organization\Sponsor;
use App\Models\Core\SponsorshipType;
use Illuminate\Http\Request;

class TeamSponsorshipController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica Completa)
    public function index(Request $request)
    {
        // Eager Loading de las 3 relaciones
        $query = TeamSponsorship::with(['team', 'sponsor', 'sponsorshipType']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del patrocinador
                ->orWhereHas('sponsor', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por tipo de patrocinio
                ->orWhereHas('sponsorshipType', function($sq) use ($search) {
                    $sq->where('type_name', 'like', '%' . $search . '%');
                });
            });
        }

        $contracts = $query->orderBy('team_sponsorship_id', 'desc')->paginate(10);
        $contracts->appends(['search' => $request->search]);

        return view('organization.team_sponsorships.index', compact('contracts'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar catálogos filtrando duplicados visuales
        $teams = Team::where('is_active', 1)->orderBy('name')->get()->unique('name');
        $sponsors = Sponsor::where('is_active', 1)->orderBy('name')->get()->unique('name');
        $types = SponsorshipType::where('is_active', 1)->orderBy('type_name')->get();
        
        return view('organization.team_sponsorships.create', compact('teams', 'sponsors', 'types'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'sponsor_id' => 'required|exists:sponsor,sponsor_id',
            'sponsorship_type_id' => 'required|exists:sponsorship_type,sponsorship_type_id',
            'deal_value_eur' => 'required|numeric|min:0',
        ]);

        TeamSponsorship::create($validated);
        return redirect()->route('team-sponsorships.index')->with('success', 'Sponsorship deal created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargar relaciones para mostrar nombres
        $contract = TeamSponsorship::with(['team', 'sponsor', 'sponsorshipType'])->findOrFail($id);
        return view('organization.team_sponsorships.show', compact('contract'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // PK: team_sponsorship_id
        $contract = TeamSponsorship::findOrFail($id);
        
        $teams = Team::where('is_active', 1)->orderBy('name')->get()->unique('name');
        $sponsors = Sponsor::where('is_active', 1)->orderBy('name')->get()->unique('name');
        $types = SponsorshipType::where('is_active', 1)->orderBy('type_name')->get();

        return view('organization.team_sponsorships.edit', compact('contract', 'teams', 'sponsors', 'types'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'sponsor_id' => 'required|exists:sponsor,sponsor_id',
            'sponsorship_type_id' => 'required|exists:sponsorship_type,sponsorship_type_id',
            'deal_value_eur' => 'required|numeric|min:0',
        ]);

        $contract = TeamSponsorship::findOrFail($id);
        $contract->update($validated);

        return redirect()->route('team-sponsorships.index')->with('success', 'Sponsorship deal updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $contract = TeamSponsorship::findOrFail($id);
        $contract->delete();
        return redirect()->route('team-sponsorships.index')->with('success', 'Sponsorship deal deleted successfully.');
    }
}