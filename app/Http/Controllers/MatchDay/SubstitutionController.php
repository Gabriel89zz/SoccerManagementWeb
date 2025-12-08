<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\Substitution;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use Illuminate\Http\Request;

class SubstitutionController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Substitution::with(['match.homeTeam', 'match.awayTeam', 'team', 'playerIn', 'playerOut']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del jugador entrante
                $q->whereHas('playerIn', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por nombre del jugador saliente
                ->orWhereHas('playerOut', function($sq) use ($search) {
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

        $substitutions = $query->orderBy('substitution_id', 'desc')->paginate(10);
        $substitutions->appends(['search' => $request->search]);

        return view('match_day.substitutions.index', compact('substitutions'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.substitutions.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_in_id' => 'required|exists:player,player_id',
            'player_out_id' => 'required|exists:player,player_id|different:player_in_id',
            'minute' => 'required|integer|min:1|max:130',
            'reason' => 'nullable|string|max:255',
        ]);

        Substitution::create($validated);
        return redirect()->route('substitutions.index')->with('success', 'Substitution registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $substitution = Substitution::with(['match.homeTeam', 'match.awayTeam', 'team', 'playerIn', 'playerOut'])->findOrFail($id);
        return view('match_day.substitutions.show', compact('substitution'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para el pre-llenado
        $substitution = Substitution::with(['playerIn', 'playerOut', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista gigante de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.substitutions.edit', compact('substitution', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_in_id' => 'required|exists:player,player_id',
            'player_out_id' => 'required|exists:player,player_id|different:player_in_id',
            'minute' => 'required|integer|min:1|max:130',
            'reason' => 'nullable|string|max:255',
        ]);

        $substitution = Substitution::findOrFail($id);
        $substitution->update($validated);

        return redirect()->route('substitutions.index')->with('success', 'Substitution updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $substitution = Substitution::findOrFail($id);
        $substitution->delete();
        return redirect()->route('substitutions.index')->with('success', 'Substitution deleted successfully.');
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