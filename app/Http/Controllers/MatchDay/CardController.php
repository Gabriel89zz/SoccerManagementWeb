<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\Card;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use Illuminate\Http\Request;

class CardController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Card::with(['match.homeTeam', 'match.awayTeam', 'team', 'player']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('player', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('team', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('card_type', 'like', '%' . $search . '%');
            });
        }

        $cards = $query->orderBy('card_id', 'desc')->paginate(10);
        $cards->appends(['search' => $request->search]);

        return view('match_day.cards.index', compact('cards'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        return view('match_day.cards.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'required|exists:player,player_id',
            'minute' => 'required|integer|min:1|max:130',
            'card_type' => 'required|in:Yellow,Red',
            'reason' => 'nullable|string|max:255',
        ]);

        Card::create($validated);
        return redirect()->route('cards.index')->with('success', 'Card registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $card = Card::with(['match.homeTeam', 'match.awayTeam', 'team', 'player'])->findOrFail($id);
        return view('match_day.cards.show', compact('card'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        // Cargar contexto del partido para pre-llenar el select
        $card = Card::with(['player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);

        // YA NO cargamos la lista gigante de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.cards.edit', compact('card', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'required|exists:player,player_id',
            'minute' => 'required|integer|min:1|max:130',
            'card_type' => 'required|in:Yellow,Red',
            'reason' => 'nullable|string|max:255',
        ]);

        $card = Card::findOrFail($id);
        $card->update($validated);

        return redirect()->route('cards.index')->with('success', 'Card updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $card = Card::findOrFail($id);
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
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