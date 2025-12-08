<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\ScoutingReport;
use App\Models\People\Scout;
use App\Models\People\Player;
use App\Models\MatchDay\MatchGame;
use Illuminate\Http\Request;

class ScoutingReportController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = ScoutingReport::with(['scout', 'player', 'match.homeTeam', 'match.awayTeam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('scout', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhere('summary_text', 'like', '%' . $search . '%');
            });
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(10);
        $reports->appends(['search' => $request->search]);

        return view('management.scouting_reports.index', compact('reports'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // Cargar scouts (son pocos, carga normal)
        $scouts = Scout::where('is_active', 1)->orderBy('last_name')->get();
        
        // YA NO cargamos players ni matches masivamente. Se cargarán por AJAX.
        
        return view('management.scouting_reports.create', compact('scouts'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_date' => 'required|date',
            'scout_id' => 'required|exists:scout,scout_id',
            'scouted_player_id' => 'required|exists:player,player_id',
            'match_observed_id' => 'nullable|exists:match,match_id',
            'overall_rating' => 'required|integer|min:1|max:100',
            'summary_text' => 'nullable|string',
        ]);

        ScoutingReport::create($validated);
        return redirect()->route('scouting-reports.index')->with('success', 'Scouting report created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $report = ScoutingReport::with(['scout', 'player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        return view('management.scouting_reports.show', compact('report'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargar reporte con relaciones para pre-llenado AJAX
        $report = ScoutingReport::with(['player', 'match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        $scouts = Scout::where('is_active', 1)->orderBy('last_name')->get();

        return view('management.scouting_reports.edit', compact('report', 'scouts'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'report_date' => 'required|date',
            'scout_id' => 'required|exists:scout,scout_id',
            'scouted_player_id' => 'required|exists:player,player_id',
            'match_observed_id' => 'nullable|exists:match,match_id',
            'overall_rating' => 'required|integer|min:1|max:100',
            'summary_text' => 'nullable|string',
        ]);

        $report = ScoutingReport::findOrFail($id);
        $report->update($validated);

        return redirect()->route('scouting-reports.index')->with('success', 'Scouting report updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $report = ScoutingReport::findOrFail($id);
        $report->delete();
        return redirect()->route('scouting-reports.index')->with('success', 'Scouting report deleted successfully.');
    }

    // 8. AJAX JUGADORES
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

    // 9. AJAX PARTIDOS
    public function searchMatches(Request $request)
    {
        $term = $request->get('q');
        if (empty($term)) return response()->json(['results' => []]);

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