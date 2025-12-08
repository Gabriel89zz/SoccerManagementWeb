<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\Foul;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use Illuminate\Http\Request;

class FoulController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Foul::with([
            'match.homeTeam', 
            'match.awayTeam', 
            'foulingTeam', 
            'fouledTeam', 
            'offender', 
            'victim'
        ]);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del infractor
                $q->whereHas('offender', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por nombre de la víctima
                ->orWhereHas('victim', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por minuto
                ->orWhere('minute', 'like', '%' . $search . '%')
                // O por nombre del equipo infractor
                ->orWhereHas('foulingTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del equipo víctima
                ->orWhereHas('fouledTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre de los equipos del partido
                ->orWhereHas('match.homeTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('match.awayTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $fouls = $query->orderBy('foul_id', 'desc')->paginate(10);
        $fouls->appends(['search' => $request->search]);

        return view('match_day.fouls.index', compact('fouls'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.fouls.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'minute' => 'required|integer|min:1|max:130',
            'is_penalty_kick' => 'required|boolean',
            'fouling_team_id' => 'required|exists:team,team_id',
            'fouling_player_id' => 'required|exists:player,player_id',
            'fouled_team_id' => 'nullable|exists:team,team_id',
            'fouled_player_id' => 'nullable|exists:player,player_id',
        ]);

        Foul::create($validated);
        return redirect()->route('fouls.index')->with('success', 'Foul registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $foul = Foul::with([
            'match.homeTeam', 
            'match.awayTeam', 
            'foulingTeam', 
            'fouledTeam', 
            'offender', 
            'victim'
        ])->findOrFail($id);
        
        return view('match_day.fouls.show', compact('foul'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones necesarias para pre-llenar los selects (incluyendo match teams)
        $foul = Foul::with(['offender', 'victim', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista masiva de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.fouls.edit', compact('foul', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'minute' => 'required|integer|min:1|max:130',
            'is_penalty_kick' => 'required|boolean',
            'fouling_team_id' => 'required|exists:team,team_id',
            'fouling_player_id' => 'required|exists:player,player_id',
            'fouled_team_id' => 'nullable|exists:team,team_id',
            'fouled_player_id' => 'nullable|exists:player,player_id',
        ]);

        $foul = Foul::findOrFail($id);
        $foul->update($validated);

        return redirect()->route('fouls.index')->with('success', 'Foul updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $foul = Foul::findOrFail($id);
        $foul->delete();
        return redirect()->route('fouls.index')->with('success', 'Foul deleted successfully.');
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