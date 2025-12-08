<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\TeamMatchStat;
use App\Models\Organization\Team;
use App\Models\MatchDay\MatchGame;
use Illuminate\Http\Request;

class TeamMatchStatController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = TeamMatchStat::with(['match.homeTeam', 'match.awayTeam', 'team']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('match', function($sq) use ($search) {
                    $sq->whereDate('match_date', 'like', '%' . $search . '%');
                });
            });
        }

        $stats = $query->orderBy('team_match_stat_id', 'desc')->paginate(10);
        $stats->appends(['search' => $request->search]);

        return view('stats.team_match_stats.index', compact('stats'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        
        return view('stats.team_match_stats.create', compact('teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'possession_percentage' => 'required|numeric|min:0|max:100',
            'corners' => 'required|integer|min:0',
            'offsides' => 'required|integer|min:0',
        ]);

        $exists = TeamMatchStat::where('match_id', $request->match_id)
                               ->where('team_id', $request->team_id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Stats for this team in this match already exist.'])->withInput();
        }

        TeamMatchStat::create($validated);
        return redirect()->route('team-match-stats.index')->with('success', 'Match stats recorded successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $stat = TeamMatchStat::with(['match.homeTeam', 'match.awayTeam', 'team'])->findOrFail($id);
        return view('stats.team_match_stats.show', compact('stat'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para el pre-llenado AJAX
        $stat = TeamMatchStat::with(['match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista masiva de partidos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('stats.team_match_stats.edit', compact('stat', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'possession_percentage' => 'required|numeric|min:0|max:100',
            'corners' => 'required|integer|min:0',
            'offsides' => 'required|integer|min:0',
        ]);

        $exists = TeamMatchStat::where('match_id', $request->match_id)
                               ->where('team_id', $request->team_id)
                               ->where('team_match_stat_id', '!=', $id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Stats for this team in this match already exist.'])->withInput();
        }

        $stat = TeamMatchStat::findOrFail($id);
        $stat->update($validated);

        return redirect()->route('team-match-stats.index')->with('success', 'Match stats updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $stat = TeamMatchStat::findOrFail($id);
        $stat->delete();
        return redirect()->route('team-match-stats.index')->with('success', 'Match stats deleted successfully.');
    }

    // 8. AJAX PARTIDOS (NUEVO MÉTODO)
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