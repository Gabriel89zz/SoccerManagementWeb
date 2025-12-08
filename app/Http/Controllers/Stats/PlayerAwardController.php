<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\PlayerAward;
use App\Models\Core\Award;
use App\Models\People\Player;
use App\Models\Competition\Season;
use Illuminate\Http\Request;

class PlayerAwardController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = PlayerAward::with(['award', 'player', 'season']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('award', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('season', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $playerAwards = $query->orderBy('player_award_id', 'desc')->paginate(10);
        $playerAwards->appends(['search' => $request->search]);

        return view('stats.player_awards.index', compact('playerAwards'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // Cargar catálogos pequeños
        $awards = Award::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();
        
        // YA NO cargamos $players masivamente. Se cargarán por AJAX.
        return view('stats.player_awards.create', compact('awards', 'seasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'award_id' => 'required|exists:award,award_id',
            'player_id' => 'required|exists:player,player_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        $exists = PlayerAward::where('award_id', $request->award_id)
                             ->where('player_id', $request->player_id)
                             ->where('season_id', $request->season_id)
                             ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This award is already assigned to this player for the selected season.'])->withInput();
        }

        PlayerAward::create($validated);
        return redirect()->route('player-awards.index')->with('success', 'Player award recorded successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $playerAward = PlayerAward::with(['award', 'player', 'season'])->findOrFail($id);
        return view('stats.player_awards.show', compact('playerAward'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos la relación player para el pre-llenado AJAX
        $playerAward = PlayerAward::with('player')->findOrFail($id);
        
        $awards = Award::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();

        // YA NO cargamos $players masivamente
        return view('stats.player_awards.edit', compact('playerAward', 'awards', 'seasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'award_id' => 'required|exists:award,award_id',
            'player_id' => 'required|exists:player,player_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        $playerAward = PlayerAward::findOrFail($id);
        $playerAward->update($validated);

        return redirect()->route('player-awards.index')->with('success', 'Player award updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $playerAward = PlayerAward::findOrFail($id);
        $playerAward->delete();
        return redirect()->route('player-awards.index')->with('success', 'Player award deleted successfully.');
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