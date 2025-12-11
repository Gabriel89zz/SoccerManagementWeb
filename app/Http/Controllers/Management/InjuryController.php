<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Injury;
use App\Models\People\Player;
use App\Models\Core\InjuryType;
use App\Models\MatchDay\MatchGame;
use Illuminate\Http\Request;

class InjuryController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Injury::with(['player', 'injuryType', 'match.homeTeam', 'match.awayTeam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('player', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('injuryType', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $injuries = $query->orderBy('date_incurred', 'desc')->paginate(10);
        $injuries->appends(['search' => $request->search]);

        return view('management.injuries.index', compact('injuries'));
    }

    // 2. CREATE FORM 
    public function create()
    {
        // YA NO cargamos players ni matches masivamente. Se cargarán por AJAX.
        $injuryTypes = InjuryType::where('is_active', 1)->orderBy('name')->get();

        return view('management.injuries.create', compact('injuryTypes'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'injury_type_id' => 'required|exists:injury_type,injury_type_id',
            'date_incurred' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:date_incurred',
            'actual_return_date' => 'nullable|date|after_or_equal:date_incurred',
            'match_id_incurred' => 'nullable|exists:match,match_id',
        ]);

        Injury::create($validated);
        return redirect()->route('injuries.index')->with('success', 'Injury report created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $injury = Injury::with(['player', 'injuryType', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        return view('management.injuries.show', compact('injury'));
    }

    // 5. EDIT FORM 
    public function edit($id)
    {
        // Cargamos relaciones para pre-llenar los inputs AJAX
        $injury = Injury::with(['player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);

        $injuryTypes = InjuryType::where('is_active', 1)->orderBy('name')->get();

        return view('management.injuries.edit', compact('injury', 'injuryTypes'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'injury_type_id' => 'required|exists:injury_type,injury_type_id',
            'date_incurred' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:date_incurred',
            'actual_return_date' => 'nullable|date|after_or_equal:date_incurred',
            'match_id_incurred' => 'nullable|exists:match,match_id',
        ]);

        $injury = Injury::findOrFail($id);
        $injury->update($validated);

        return redirect()->route('injuries.index')->with('success', 'Injury report updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $injury = Injury::findOrFail($id);
        $injury->delete();
        return redirect()->route('injuries.index')->with('success', 'Injury report deleted successfully.');
    }

    // 8. AJAX JUGADORES
    public function searchPlayers(Request $request)
    {
        $term = $request->get('q');
        if (empty($term))
            return response()->json(['results' => []]);

        $players = Player::where('is_active', 1)
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'like', '%' . $term . '%')
                    ->orWhere('last_name', 'like', '%' . $term . '%');
            })
            ->with('country')
            ->orderBy('last_name')
            ->limit(20)
            ->get();

        $results = $players->map(function ($player) {
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

    // 9. AJAX PARTIDOS
    public function searchMatches(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where(function ($query) use ($term) {
                $query->whereHas('homeTeam', function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                })
                    ->orWhereHas('awayTeam', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
            })
            ->orderBy('match_date', 'desc')
            ->limit(20)
            ->get();

        $results = $matches->map(function ($m) {
            return [
                'id' => $m->match_id,
                'text' => ($m->homeTeam->name ?? '?') . ' vs ' . ($m->awayTeam->name ?? '?') . ' (' . ($m->match_date ? $m->match_date->format('d/m/Y') : 'TBD') . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }
}