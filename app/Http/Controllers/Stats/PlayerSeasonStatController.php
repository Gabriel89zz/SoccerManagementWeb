<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\PlayerSeasonStat;
use App\Models\People\Player;
use App\Models\Organization\Team;
use App\Models\Competition\CompetitionSeason;
use Illuminate\Http\Request;

class PlayerSeasonStatController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = PlayerSeasonStat::with(['player', 'team', 'competitionSeason.competition', 'competitionSeason.season']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('competitionSeason.competition', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $stats = $query->orderBy('player_season_stat_id', 'desc')->paginate(10);
        $stats->appends(['search' => $request->search]);

        return view('stats.player_season_stats.index', compact('stats'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $players masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();
        
        return view('stats.player_season_stats.create', compact('teams', 'compSeasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'team_id' => 'required|exists:team,team_id',
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'matches_played' => 'required|integer|min:0',
            'minutes_played' => 'required|integer|min:0',
            'goals' => 'required|integer|min:0',
            'assists' => 'required|integer|min:0',
            'yellow_cards' => 'required|integer|min:0',
            'red_cards' => 'required|integer|min:0',
            'shots_on_target' => 'required|integer|min:0',
        ]);

        $exists = PlayerSeasonStat::where('player_id', $request->player_id)
                                  ->where('competition_season_id', $request->competition_season_id)
                                  ->where('team_id', $request->team_id)
                                  ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Stats record for this player in this competition season already exists.'])->withInput();
        }

        PlayerSeasonStat::create($validated);
        return redirect()->route('player-season-stats.index')->with('success', 'Season stats recorded successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $stat = PlayerSeasonStat::with(['player', 'team', 'competitionSeason.competition', 'competitionSeason.season'])->findOrFail($id);
        return view('stats.player_season_stats.show', compact('stat'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos la relación player para pre-llenar el input AJAX
        $stat = PlayerSeasonStat::with('player')->findOrFail($id);
        
        // YA NO cargamos la lista masiva de jugadores
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $compSeasons = CompetitionSeason::with(['competition', 'season'])
                                        ->where('is_active', 1)
                                        ->get();

        return view('stats.player_season_stats.edit', compact('stat', 'teams', 'compSeasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'team_id' => 'required|exists:team,team_id',
            'competition_season_id' => 'required|exists:competition_season,competition_season_id',
            'matches_played' => 'required|integer|min:0',
            'minutes_played' => 'required|integer|min:0',
            'goals' => 'required|integer|min:0',
            'assists' => 'required|integer|min:0',
            'yellow_cards' => 'required|integer|min:0',
            'red_cards' => 'required|integer|min:0',
            'shots_on_target' => 'required|integer|min:0',
        ]);

        $exists = PlayerSeasonStat::where('player_id', $request->player_id)
                                  ->where('competition_season_id', $request->competition_season_id)
                                  ->where('team_id', $request->team_id)
                                  ->where('player_season_stat_id', '!=', $id)
                                  ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Stats record for this player in this competition season already exists.'])->withInput();
        }

        $stat = PlayerSeasonStat::findOrFail($id);
        $stat->update($validated);

        return redirect()->route('player-season-stats.index')->with('success', 'Season stats updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $stat = PlayerSeasonStat::findOrFail($id);
        $stat->delete();
        return redirect()->route('player-season-stats.index')->with('success', 'Stats record deleted successfully.');
    }

    // 8. AJAX JUGADORES (NUEVO MÉTODO)
    public function searchPlayers(Request $request)
    {
        $term = $request->get('q');
        if (empty($term)) return response()->json(['results' => []]);

        $players = Player::where('is_active', 1)
                         ->where(function($query) use ($term) {
                             $query->where('first_name', 'like', '%' . $term . '%')
                                   ->orWhere('last_name', 'like', '%' . $term . '%');
                         })
                         ->with('country')
                         ->orderBy('last_name')
                         ->limit(20)
                         ->get();

        $results = $players->map(function($player) {
            return [
                'id' => $player->player_id,
                'text' => $player->full_name . ' (' . ($player->country->name ?? 'N/A') . ')',
                'firstName' => $player->first_name,
                'lastName' => $player->last_name,
                'country' => $player->country->name ?? 'N/A'
            ];
        });

        return response()->json(['results' => $results]);
    }
}