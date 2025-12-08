<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\LeagueStanding;
use App\Models\Competition\CompetitionSeason;
use App\Models\Organization\Team;
use Illuminate\Http\Request;

class LeagueStandingController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: CompeticiónTemporada (con sus padres) y Equipo
        $query = LeagueStanding::with(['competitionSeason.competition', 'competitionSeason.season', 'team']);

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

        // Ordenamos por Competición/Temporada y luego por Ranking (Posición)
        $standings = $query->orderBy('competition_season_id')
                           ->orderBy('rank', 'asc')
                           ->paginate(15);
                           
        $standings->appends(['search' => $request->search]);

        return view('stats.league_standings.index', compact('standings'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar Temporadas de Competición activas
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();

        // Cargar equipos activos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        
        return view('stats.league_standings.create', compact('compSeasons', 'teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'team_id' => 'required|exists:team,team_id',
            'rank' => 'required|integer|min:1',
            'played' => 'required|integer|min:0',
            'won' => 'required|integer|min:0',
            'drawn' => 'required|integer|min:0',
            'lost' => 'required|integer|min:0',
            'goals_for' => 'required|integer|min:0',
            'goals_against' => 'required|integer|min:0',
            'goal_difference' => 'required|integer',
            'points' => 'required|integer|min:0',
        ]);

        // Validar duplicados: El mismo equipo no puede estar dos veces en la misma tabla de liga
        $exists = LeagueStanding::where('competition_season_id', $request->competition_season_id)
                                ->where('team_id', $request->team_id)
                                ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already in this league standing.'])->withInput();
        }

        LeagueStanding::create($validated);
        return redirect()->route('league-standings.index')->with('success', 'League standing record created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $standing = LeagueStanding::with(['competitionSeason.competition', 'competitionSeason.season', 'team'])->findOrFail($id);
        return view('stats.league_standings.show', compact('standing'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $standing = LeagueStanding::findOrFail($id);
        
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();

        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('stats.league_standings.edit', compact('standing', 'compSeasons', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'team_id' => 'required|exists:team,team_id',
            'rank' => 'required|integer|min:1',
            'played' => 'required|integer|min:0',
            'won' => 'required|integer|min:0',
            'drawn' => 'required|integer|min:0',
            'lost' => 'required|integer|min:0',
            'goals_for' => 'required|integer|min:0',
            'goals_against' => 'required|integer|min:0',
            'goal_difference' => 'required|integer',
            'points' => 'required|integer|min:0',
        ]);

        // Validar duplicados excluyendo actual
        $exists = LeagueStanding::where('competition_season_id', $request->competition_season_id)
                                ->where('team_id', $request->team_id)
                                ->where('league_standing_id', '!=', $id)
                                ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already in this league standing.'])->withInput();
        }

        $standing = LeagueStanding::findOrFail($id);
        $standing->update($validated);

        return redirect()->route('league-standings.index')->with('success', 'League standing updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $standing = LeagueStanding::findOrFail($id);
        $standing->delete();
        return redirect()->route('league-standings.index')->with('success', 'League standing record deleted successfully.');
    }
}