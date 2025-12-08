<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\Group;
use App\Models\Competition\CompetitionStage;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading profundo para dar contexto (Qué competición/temporada es)
        $query = Group::with(['stage.competitionSeason.competition', 'stage.competitionSeason.season']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del grupo
                $q->where('group_name', 'like', '%' . $search . '%')
                  // O por nombre de la fase
                  ->orWhereHas('stage', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // O por nombre de la competición (via stage -> competitionSeason -> competition)
                  ->orWhereHas('stage.competitionSeason.competition', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $groups = $query->orderBy('group_name')->paginate(10);
        $groups->appends(['search' => $request->search]);

        return view('competition.groups.index', compact('groups'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar Fases activas con contexto para el select
        // Mostraremos: "Competición (Temporada) - Fase"
        $stages = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season'])
                                  ->where('is_active', 1)
                                  ->get();
        
        return view('competition.groups.create', compact('stages'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_name' => 'required|max:100',
            'qualification_slots' => 'required|integer|min:0',
            'stage_id' => 'required|exists:competiton_stage,stage_id', // Ojo con el nombre de tabla en tu modelo Stage
        ]);

        Group::create($validated);
        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        // Cargar relaciones profundas
        $group = Group::with(['stage.competitionSeason.competition', 'stage.competitionSeason.season'])->findOrFail($id);
        return view('competition.groups.show', compact('group'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $group = Group::findOrFail($id);
        
        $stages = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season'])
                                  ->where('is_active', 1)
                                  ->get();

        return view('competition.groups.edit', compact('group', 'stages'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_name' => 'required|max:100',
            'qualification_slots' => 'required|integer|min:0',
            'stage_id' => 'required|exists:competiton_stage,stage_id',
        ]);

        $group = Group::findOrFail($id);
        $group->update($validated);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}