<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\Shot;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use Illuminate\Http\Request;

class ShotController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Shot::with(['match.homeTeam', 'match.awayTeam', 'team', 'player']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del jugador
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por nombre del equipo
                ->orWhereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por minuto
                ->orWhere('minute', 'like', '%' . $search . '%');
            });
        }

        $shots = $query->orderBy('shot_id', 'desc')->paginate(10);
        $shots->appends(['search' => $request->search]);

        return view('match_day.shots.index', compact('shots'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.shots.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'required|exists:player,player_id',
            'minute' => 'required|integer|min:1|max:130',
            'is_on_target' => 'required|boolean',
            'is_goal' => 'required|boolean',
            'body_part' => 'nullable|string|max:50',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
        ]);

        Shot::create($validated);
        return redirect()->route('shots.index')->with('success', 'Shot registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $shot = Shot::with(['match.homeTeam', 'match.awayTeam', 'team', 'player'])->findOrFail($id);
        return view('match_day.shots.show', compact('shot'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para el pre-llenado
        $shot = Shot::with(['player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista gigante de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.shots.edit', compact('shot', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'required|exists:player,player_id',
            'minute' => 'required|integer|min:1|max:130',
            'is_on_target' => 'required|boolean',
            'is_goal' => 'required|boolean',
            'body_part' => 'nullable|string|max:50',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
        ]);

        $shot = Shot::findOrFail($id);
        $shot->update($validated);

        return redirect()->route('shots.index')->with('success', 'Shot updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $shot = Shot::findOrFail($id);
        $shot->delete();
        return redirect()->route('shots.index')->with('success', 'Shot deleted successfully.');
    }

    // 8. AJAX JUGADORES (Existente)
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

    // 9. AJAX PARTIDOS (NUEVO MÉTODO)
    public function searchMatches(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where(function($query) use ($term) {
                $query->whereHas('homeTeam', function($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                })
                ->orWhereHas('awayTeam', function($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                });
            })
            ->orderBy('match_date', 'desc')
            ->limit(20)
            ->get();

        $results = $matches->map(function($m) {
            return [
                'id' => $m->match_id,
                'text' => ($m->homeTeam->name ?? '?') . ' vs ' . ($m->awayTeam->name ?? '?') . ' (' . ($m->match_date ? $m->match_date->format('d/m/Y') : 'TBD') . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }
}