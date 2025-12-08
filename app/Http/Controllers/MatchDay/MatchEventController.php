<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\MatchEvent;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\People\Player;
use App\Models\Core\EventType;
use Illuminate\Http\Request;

class MatchEventController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = MatchEvent::with(['match.homeTeam', 'match.awayTeam', 'team', 'player', 'eventType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('eventType', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('minute', 'like', '%' . $search . '%');
            });
        }

        $events = $query->orderBy('event_id', 'desc')->paginate(10);
        $events->appends(['search' => $request->search]);

        return view('match_day.match_events.index', compact('events'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        // Los tipos de evento suelen ser pocos, se pueden dejar cargados
        $eventTypes = EventType::where('is_active', 1)->orderBy('name')->get();
        
        return view('match_day.match_events.create', compact('teams', 'eventTypes'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'minute' => 'required|integer|min:1|max:130',
            'event_type_id' => 'required|exists:event_type,event_type_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'nullable|exists:player,player_id',
        ]);

        MatchEvent::create($validated);
        return redirect()->route('match-events.index')->with('success', 'Match event registered successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $event = MatchEvent::with(['match.homeTeam', 'match.awayTeam', 'team', 'player', 'eventType'])->findOrFail($id);
        return view('match_day.match_events.show', compact('event'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para el pre-llenado
        $event = MatchEvent::with(['player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista gigante de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $eventTypes = EventType::where('is_active', 1)->orderBy('name')->get();

        return view('match_day.match_events.edit', compact('event', 'teams', 'eventTypes'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'minute' => 'required|integer|min:1|max:130',
            'event_type_id' => 'required|exists:event_type,event_type_id',
            'team_id' => 'required|exists:team,team_id',
            'player_id' => 'nullable|exists:player,player_id',
        ]);

        $event = MatchEvent::findOrFail($id);
        $event->update($validated);

        return redirect()->route('match-events.index')->with('success', 'Match event updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $event = MatchEvent::findOrFail($id);
        $event->delete();
        return redirect()->route('match-events.index')->with('success', 'Match event deleted successfully.');
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