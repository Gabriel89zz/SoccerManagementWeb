<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats\GroupStanding;
use App\Models\Competition\Group;
use App\Models\Organization\Team;
use Illuminate\Http\Request;

class GroupStandingController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Grupo (con contexto de competición) y Equipo
        $query = GroupStanding::with(['group.stage.competitionSeason.competition', 'team']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre del grupo
                ->orWhereHas('group', function($sq) use ($search) {
                    $sq->where('group_name', 'like', '%' . $search . '%');
                });
            });
        }

        // Ordenamos por Grupo y luego por Ranking (Posición)
        $standings = $query->orderBy('group_id')
                           ->orderBy('rank', 'asc')
                           ->paginate(15); // Un poco más de items por página para ver tablas completas
                           
        $standings->appends(['search' => $request->search]);

        return view('stats.group_standings.index', compact('standings'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar grupos con contexto
        $groups = Group::with(['stage.competitionSeason.competition', 'stage.competitionSeason.season'])
                       ->where('is_active', 1)
                       ->get();

        // Cargar equipos activos
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        
        return view('stats.group_standings.create', compact('groups', 'teams'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:group,group_id', // Ojo: tabla 'group'
            'team_id' => 'required|exists:team,team_id',
            'rank' => 'required|integer|min:1',
            'played' => 'required|integer|min:0',
            'won' => 'required|integer|min:0',
            'drawn' => 'required|integer|min:0',
            'lost' => 'required|integer|min:0',
            'goals_for' => 'required|integer|min:0',
            'goals_against' => 'required|integer|min:0',
            'goal_difference' => 'required|integer',
            'points' => 'required|integer|min:0',
        ]);

        // Validar duplicados: El mismo equipo no puede estar dos veces en el mismo grupo
        $exists = GroupStanding::where('group_id', $request->group_id)
                               ->where('team_id', $request->team_id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already in this group standing.'])->withInput();
        }

        GroupStanding::create($validated);
        return redirect()->route('group-standings.index')->with('success', 'Standing record created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $standing = GroupStanding::with(['group.stage.competitionSeason.competition', 'team'])->findOrFail($id);
        return view('stats.group_standings.show', compact('standing'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $standing = GroupStanding::findOrFail($id);
        
        $groups = Group::with(['stage.competitionSeason.competition', 'stage.competitionSeason.season'])
                       ->where('is_active', 1)
                       ->get();

        $teams = Team::where('is_active', 1)->orderBy('name')->get();

        return view('stats.group_standings.edit', compact('standing', 'groups', 'teams'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:group,group_id',
            'team_id' => 'required|exists:team,team_id',
            'rank' => 'required|integer|min:1',
            'played' => 'required|integer|min:0',
            'won' => 'required|integer|min:0',
            'drawn' => 'required|integer|min:0',
            'lost' => 'required|integer|min:0',
            'goals_for' => 'required|integer|min:0',
            'goals_against' => 'required|integer|min:0',
            'goal_difference' => 'required|integer',
            'points' => 'required|integer|min:0',
        ]);

        // Validar duplicados excluyendo actual
        $exists = GroupStanding::where('group_id', $request->group_id)
                               ->where('team_id', $request->team_id)
                               ->where('group_standing_id', '!=', $id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This team is already in this group standing.'])->withInput();
        }

        $standing = GroupStanding::findOrFail($id);
        $standing->update($validated);

        return redirect()->route('group-standings.index')->with('success', 'Standing updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $standing = GroupStanding::findOrFail($id);
        $standing->delete();
        return redirect()->route('group-standings.index')->with('success', 'Standing record deleted successfully.');
    }
}