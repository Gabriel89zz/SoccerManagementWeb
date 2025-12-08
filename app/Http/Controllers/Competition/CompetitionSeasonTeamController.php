<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\CompetitionSeasonTeam;
use App\Models\Competition\CompetitionSeason;
use App\Models\Organization\Team; // Ajusta el namespace de Team si es diferente en tu proyecto (e.g. Organization\Team)
use Illuminate\Http\Request;

class CompetitionSeasonTeamController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading profundo para mostrar: Equipo, Competición y Temporada
        $query = CompetitionSeasonTeam::with(['team', 'competitionSeason.competition', 'competitionSeason.season']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre de la competición
                ->orWhereHas('competitionSeason.competition', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre de la temporada
                ->orWhereHas('competitionSeason.season', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $participations = $query->orderBy('competition_season_team_id', 'asc')->paginate(10);
        $participations->appends(['search' => $request->search]);

        return view('competition.competition_season_teams.index', compact('participations'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar CompetitionSeasons con relaciones para el dropdown "Competición - Temporada"
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();
        
        // Formatear para el select (esto podría hacerse en la vista, pero aquí queda limpio)
        // No es necesario modificar la colección, lo haremos en el blade.

        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        
        return view('competition.competition_season_teams.create', compact('compSeasons', 'teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'team_id' => 'required|exists:team,team_id',
            'final_position' => 'nullable|integer|min:1',
            'overall_status' => 'nullable|max:100', // e.g. "Champion", "Relegated"
        ]);

        // Validar duplicados (mismo equipo en la misma temporada de competición)
        $exists = CompetitionSeasonTeam::where('competition_season_id', $request->competition_season_id)
                                       ->where('team_id', $request->team_id)
                                       ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already registered in this competition season.'])->withInput();
        }

        CompetitionSeasonTeam::create($validated);
        return redirect()->route('competition-season-teams.index')->with('success', 'Team participation registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $participation = CompetitionSeasonTeam::with(['team', 'competitionSeason.competition', 'competitionSeason.season'])->findOrFail($id);
        return view('competition.competition_season_teams.show', compact('participation'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $participation = CompetitionSeasonTeam::findOrFail($id);
        
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();

        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('competition.competition_season_teams.edit', compact('participation', 'compSeasons', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'team_id' => 'required|exists:team,team_id',
            'final_position' => 'nullable|integer|min:1',
            'overall_status' => 'nullable|max:100',
        ]);

        // Validar duplicados excluyendo el actual
        $exists = CompetitionSeasonTeam::where('competition_season_id', $request->competition_season_id)
                                       ->where('team_id', $request->team_id)
                                       ->where('competition_season_team_id', '!=', $id)
                                       ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already registered in this competition season.'])->withInput();
        }

        $participation = CompetitionSeasonTeam::findOrFail($id);
        $participation->update($validated);

        return redirect()->route('competition-season-teams.index')->with('success', 'Team participation updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $participation = CompetitionSeasonTeam::findOrFail($id);
        $participation->delete();
        return redirect()->route('competition-season-teams.index')->with('success', 'Team participation deleted successfully.');
    }
}