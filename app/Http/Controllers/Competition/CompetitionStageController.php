<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\CompetitionStage;
use App\Models\Competition\CompetitionSeason;
use Illuminate\Http\Request;

class CompetitionStageController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos la temporada de competición y sus detalles
        $query = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Buscar por nombre de la fase
                $q->where('name', 'like', '%' . $search . '%')
                    // O por nombre de la competición
                    ->orWhereHas('competitionSeason.competition', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    })
                    // O por nombre de la temporada
                    ->orWhereHas('competitionSeason.season', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Ordenamos por Temporada y luego por Orden de Fase
        $stages = $query->orderBy('competition_season_id', 'desc')
            ->orderBy('stage_order', 'asc')
            ->paginate(10);

        $stages->appends(['search' => $request->search]);

        return view('competition.competition_stages.index', compact('stages'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar CompetitionSeasons para el dropdown
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
            ->where('is_active', 1)
            ->get();

        return view('competition.competition_stages.create', compact('compSeasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'name' => 'required|max:100', // e.g. "Group Stage"
            'stage_order' => 'required|integer|min:1',
        ]);

        CompetitionStage::create($validated);
        return redirect()->route('competition-stages.index')->with('success', 'Stage created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $stage = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season'])->findOrFail($id);
        return view('competition.competition_stages.show', compact('stage'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $stage = CompetitionStage::findOrFail($id);

        $compSeasons = CompetitionSeason::with(['competition', 'season'])
            ->where('is_active', 1)
            ->get();

        return view('competition.competition_stages.edit', compact('stage', 'compSeasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'name' => 'required|max:100',
            'stage_order' => 'required|integer|min:1',
        ]);

        $stage = CompetitionStage::findOrFail($id);
        $stage->update($validated);

        return redirect()->route('competition-stages.index')->with('success', 'Stage updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $stage = CompetitionStage::findOrFail($id);
        $stage->delete();
        return redirect()->route('competition-stages.index')->with('success', 'Stage deleted successfully.');
    }
}