<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\Goal;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Goal::with(['match.homeTeam', 'match.awayTeam', 'team', 'scorer', 'assistant']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('scorer', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('minute', 'like', '%' . $search . '%');
            });
        }

        $goals = $query->orderBy('goal_id', 'desc')->paginate(10);
        $goals->appends(['search' => $request->search]);

        return view('match_day.goals.index', compact('goals'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.goals.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'scoring_team_id' => 'required|exists:team,team_id',
            'scoring_player_id' => 'required|exists:player,player_id',
            'assist_player_id' => 'nullable|exists:player,player_id|different:scoring_player_id',
            'minute' => 'required|integer|min:1|max:130',
            'body_part' => 'nullable|string|max:50',
            'is_own_goal' => 'required|boolean',
            'is_penalty' => 'required|boolean',
        ]);

        Goal::create($validated);
        return redirect()->route('goals.index')->with('success', 'Goal registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $goal = Goal::with(['match.homeTeam', 'match.awayTeam', 'team', 'scorer', 'assistant'])->findOrFail($id);
        return view('match_day.goals.show', compact('goal'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para pre-llenar el select en la vista
        $goal = Goal::with(['scorer', 'assistant', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista gigante de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.goals.edit', compact('goal', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'scoring_team_id' => 'required|exists:team,team_id',
            'scoring_player_id' => 'required|exists:player,player_id',
            'assist_player_id' => 'nullable|exists:player,player_id|different:scoring_player_id',
            'minute' => 'required|integer|min:1|max:130',
            'body_part' => 'nullable|string|max:50',
            'is_own_goal' => 'required|boolean',
            'is_penalty' => 'required|boolean',
        ]);

        $goal = Goal::findOrFail($id);
        $goal->update($validated);

        return redirect()->route('goals.index')->with('success', 'Goal updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $goal = Goal::findOrFail($id);
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully.');
    }

    // 8. AJAX JUGADORES (Tu código existente)
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

        // Buscamos partidos donde el nombre del local o visitante coincida
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
            ->limit(20) // Limitamos a 20 resultados
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