<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\LineupPlayer;
use App\Models\MatchDay\MatchLineup;
use App\Models\People\Player;
use App\Models\Core\Position;
use Illuminate\Http\Request;

class LineupPlayerController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = LineupPlayer::with(['lineup.team', 'lineup.match', 'player', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('lineup.team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $players = $query->orderBy('lineup_player_id', 'desc')->paginate(10);
        $players->appends(['search' => $request->search]);

        return view('match_day.lineup_players.index', compact('players'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $positions = Position::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.lineup_players.create', compact('positions'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_lineup_id' => 'required|exists:match_lineup,match_lineup_id',
            'player_id'       => 'required|exists:player,player_id',
            'position_id'     => 'required|exists:position,position_id',
            'is_starter'      => 'required|boolean',
            'is_captain'      => 'required|boolean',
        ]);

        $exists = LineupPlayer::where('match_lineup_id', $request->match_lineup_id)
                              ->where('player_id', $request->player_id)
                              ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This player is already in this lineup.'])->withInput();
        }

        LineupPlayer::create($validated);
        return redirect()->route('lineup-players.index')->with('success', 'Player added to lineup successfully.');
    }

    // 4. SHOW
    public function show($id)
    {
        $lineupPlayer = LineupPlayer::with(['lineup.team', 'lineup.match', 'player', 'position'])->findOrFail($id);
        return view('match_day.lineup_players.show', compact('lineupPlayer'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $lineupPlayer = LineupPlayer::with(['player', 'lineup.team', 'lineup.match.homeTeam', 'lineup.match.awayTeam'])->findOrFail($id);
        $positions = Position::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.lineup_players.edit', compact('lineupPlayer', 'positions'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_lineup_id' => 'required|exists:match_lineup,match_lineup_id',
            'player_id'       => 'required|exists:player,player_id',
            'position_id'     => 'required|exists:position,position_id',
            'is_starter'      => 'required|boolean',
            'is_captain'      => 'required|boolean',
        ]);

        $lineupPlayer = LineupPlayer::findOrFail($id);
        $lineupPlayer->update($validated);

        return redirect()->route('lineup-players.index')->with('success', 'Lineup player updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $lineupPlayer = LineupPlayer::findOrFail($id);
        $lineupPlayer->delete();
        return redirect()->route('lineup-players.index')->with('success', 'Player removed from lineup successfully.');
    }

    // --- MÉTODOS AJAX (Usados por las rutas específicas) ---

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
                'id'        => $player->player_id,
                'text'      => $player->full_name . ' (' . ($player->country->name ?? 'N/A') . ')',
                'firstName' => $player->first_name,
                'lastName'  => $player->last_name,
                'country'   => $player->country->name ?? 'N/A'
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function searchLineups(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) return response()->json(['results' => []]);

        $lineups = MatchLineup::with(['team', 'match.homeTeam', 'match.awayTeam'])
            ->where('is_active', 1)
            ->whereHas('team', function($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $results = $lineups->map(function($l) {
            $opponent = '?';
            $date = '';
            
            if ($l->match) {
                $opponent = $l->match->home_team_id == $l->team_id
                            ? ($l->match->awayTeam->name ?? '?')
                            : ($l->match->homeTeam->name ?? '?');
                $date = $l->match->match_date ? $l->match->match_date->format('Y-m-d') : '';
            }

            $text = ($l->team->name ?? 'Unknown') . " (vs $opponent) - " . $date;

            return [
                'id'   => $l->match_lineup_id,
                'text' => $text
            ];
        });

        return response()->json(['results' => $results]);
    }
}