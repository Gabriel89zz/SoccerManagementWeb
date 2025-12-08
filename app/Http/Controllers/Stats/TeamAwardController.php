<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\TeamAward;
use App\Models\Core\Award;
use App\Models\Organization\Team;
use App\Models\Competition\Season;
use Illuminate\Http\Request;

class TeamAwardController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Premio, Equipo y Temporada
        $query = TeamAward::with(['award', 'team', 'season']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del premio
                ->orWhereHas('award', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre de la temporada
                ->orWhereHas('season', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $teamAwards = $query->orderBy('team_award_id', 'desc')->paginate(10);
        $teamAwards->appends(['search' => $request->search]);

        return view('stats.team_awards.index', compact('teamAwards'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar catálogos
        $awards = Award::where('is_active', 1)->orderBy('name')->get();
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();
        
        return view('stats.team_awards.create', compact('awards', 'teams', 'seasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'award_id' => 'required|exists:award,award_id',
            'team_id' => 'required|exists:team,team_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        // Opcional: Validar duplicados (mismo equipo, mismo premio, misma temporada)
        $exists = TeamAward::where('award_id', $request->award_id)
                           ->where('team_id', $request->team_id)
                           ->where('season_id', $request->season_id)
                           ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This award is already assigned to this team for the selected season.'])->withInput();
        }

        TeamAward::create($validated);
        return redirect()->route('team-awards.index')->with('success', 'Team award recorded successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $teamAward = TeamAward::with(['award', 'team', 'season'])->findOrFail($id);
        return view('stats.team_awards.show', compact('teamAward'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $teamAward = TeamAward::findOrFail($id);
        
        $awards = Award::where('is_active', 1)->orderBy('name')->get();
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();

        return view('stats.team_awards.edit', compact('teamAward', 'awards', 'teams', 'seasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'award_id' => 'required|exists:award,award_id',
            'team_id' => 'required|exists:team,team_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        $teamAward = TeamAward::findOrFail($id);
        $teamAward->update($validated);

        return redirect()->route('team-awards.index')->with('success', 'Team award updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $teamAward = TeamAward::findOrFail($id);
        $teamAward->delete();
        return redirect()->route('team-awards.index')->with('success', 'Team award deleted successfully.');
    }
}