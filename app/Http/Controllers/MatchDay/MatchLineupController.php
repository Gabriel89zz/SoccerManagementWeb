<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\MatchLineup;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\Core\Formation;
use App\Models\People\Coach;
use Illuminate\Http\Request;

class MatchLineupController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading
        $query = MatchLineup::with(['match.homeTeam', 'match.awayTeam', 'team', 'formation', 'coach']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por formación (4-4-2, etc)
                ->orWhereHas('formation', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del entrenador
                ->orWhereHas('coach', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });
        }

        $lineups = $query->orderBy('match_lineup_id', 'desc')->paginate(10);
        $lineups->appends(['search' => $request->search]);

        return view('match_day.match_lineups.index', compact('lineups'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // SOLO cargamos los campos que no necesitan AJAX
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $formations = Formation::where('is_active', 1)->orderBy('name')->get();
        $coaches = Coach::where('is_active', 1)->orderBy('last_name')->get();
        
        return view('match_day.match_lineups.create', compact('teams', 'formations', 'coaches'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'formation_id' => 'required|exists:formation,formation_id',
            'coach_id' => 'required|exists:coach,coach_id',
        ]);

        // Validar que el equipo seleccionado realmente juegue en ese partido
        $match = MatchGame::find($request->match_id);
        if($match && ($match->home_team_id != $request->team_id && $match->away_team_id != $request->team_id)) {
            return back()->withErrors(['team_id' => 'The selected team is not playing in this match.'])->withInput();
        }

        MatchLineup::create($validated);
        return redirect()->route('match-lineups.index')->with('success', 'Lineup created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $lineup = MatchLineup::with(['match.homeTeam', 'match.awayTeam', 'team', 'formation', 'coach'])->findOrFail($id);
        return view('match_day.match_lineups.show', compact('lineup'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $lineup = MatchLineup::with('match')->findOrFail($id);
        
        // SOLO cargamos los campos que no necesitan AJAX
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $formations = Formation::where('is_active', 1)->orderBy('name')->get();
        $coaches = Coach::where('is_active', 1)->orderBy('last_name')->get();

        return view('match_day.match_lineups.edit', compact('lineup', 'teams', 'formations', 'coaches'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'team_id' => 'required|exists:team,team_id',
            'formation_id' => 'required|exists:formation,formation_id',
            'coach_id' => 'required|exists:coach,coach_id',
        ]);

        // Validar que el equipo seleccionado realmente juegue en ese partido
        $match = MatchGame::find($request->match_id);
        if($match && ($match->home_team_id != $request->team_id && $match->away_team_id != $request->team_id)) {
            return back()->withErrors(['team_id' => 'The selected team is not playing in this match.'])->withInput();
        }

        $lineup = MatchLineup::findOrFail($id);
        $lineup->update($validated);

        return redirect()->route('match-lineups.index')->with('success', 'Lineup updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $lineup = MatchLineup::findOrFail($id);
        $lineup->delete();
        return redirect()->route('match-lineups.index')->with('success', 'Lineup deleted successfully.');
    }

    // ========== MÉTODOS AJAX ==========

    /**
     * Buscar partidos por AJAX para Select2
     * Solo este campo necesita AJAX porque puede tener miles de registros
     */
   // En MatchLineupController.php - método searchMatches()
public function searchMatches(Request $request)
{
    $search = $request->get('q', '');
    
    $matches = MatchGame::with(['homeTeam', 'awayTeam'])
        ->when($search, function($query) use ($search) {
            return $query->where(function($q) use ($search) {
                // Buscar por nombre de equipos (local o visitante)
                $q->whereHas('homeTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('awayTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // Buscar por fecha (formato YYYY-MM-DD)
                ->orWhere('match_date', 'like', '%' . $search . '%');
                // NOTA: Removimos la búsqueda por ID para que los usuarios no lo vean
            });
        })
        ->orderBy('match_date', 'desc')
        ->paginate(20);

    // Formatear para Select2 - SIN MOSTRAR EL ID
    $formattedMatches = $matches->map(function($match) {
        $date = $match->match_date ? $match->match_date->format('Y-m-d H:i') : 'Date TBD';
        
        return [
            'id' => $match->match_id,
            'text' => sprintf(
                '%s vs %s - %s',
                $match->homeTeam->name ?? 'Unknown',
                $match->awayTeam->name ?? 'Unknown',
                $date
            ),
            'match_date' => $date,
            'home_team_id' => $match->home_team_id,
            'home_team' => $match->homeTeam->name ?? 'Unknown',
            'away_team_id' => $match->away_team_id,
            'away_team' => $match->awayTeam->name ?? 'Unknown'
        ];
    });

    return response()->json([
        'results' => $formattedMatches,
        'pagination' => [
            'more' => $matches->hasMorePages()
        ]
    ]);
}

    /**
     * Obtener información de un partido específico por ID
     * Para cargar datos cuando se edita un lineup existente
     */
   // En MatchLineupController.php - método getMatchInfo()
public function getMatchInfo($id)
{
    $match = MatchGame::with(['homeTeam', 'awayTeam'])->find($id);
    
    if (!$match) {
        return response()->json(['error' => 'Match not found'], 404);
    }

    return response()->json([
        'match_id' => $match->match_id,
        'home_team_id' => $match->home_team_id,
        'home_team_name' => $match->homeTeam->name ?? 'Unknown',
        'away_team_id' => $match->away_team_id,
        'away_team_name' => $match->awayTeam->name ?? 'Unknown',
        'match_date' => $match->match_date ? $match->match_date->format('Y-m-d H:i') : 'Date TBD',
        'full_text' => sprintf(
            '%s vs %s - %s',
            $match->homeTeam->name ?? 'Unknown',
            $match->awayTeam->name ?? 'Unknown',
            $match->match_date ? $match->match_date->format('Y-m-d H:i') : 'Date TBD'
        )
    ]);
}
}