<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Squad;
use App\Models\Organization\Team;
use App\Models\Competition\Season;
use Illuminate\Http\Request;

class SquadController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Equipo, Temporada y conteo de miembros
        $query = Squad::with(['team', 'season'])->withCount('members');

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del equipo
                $q->whereHas('team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                // O por nombre de la temporada
                ->orWhereHas('season', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $squads = $query->orderBy('squad_id', 'asc')->paginate(10);
        $squads->appends(['search' => $request->search]);

        return view('management.squads.index', compact('squads'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar equipos y temporadas activas
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        // Ordenar temporadas por nombre descendente (más recientes primero)
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();
        
        return view('management.squads.create', compact('teams', 'seasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        // Validar duplicados: Un equipo solo debe tener una plantilla por temporada
        $exists = Squad::where('team_id', $request->team_id)
                       ->where('season_id', $request->season_id)
                       ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'A squad for this team and season already exists.'])->withInput();
        }

        Squad::create($validated);
        return redirect()->route('squads.index')->with('success', 'Squad created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $squad = Squad::with(['team', 'season', 'members'])->findOrFail($id);
        return view('management.squads.show', compact('squad'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $squad = Squad::findOrFail($id);
        
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();

        return view('management.squads.edit', compact('squad', 'teams', 'seasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        // Validar duplicados excluyendo el actual
        $exists = Squad::where('team_id', $request->team_id)
                       ->where('season_id', $request->season_id)
                       ->where('squad_id', '!=', $id)
                       ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'A squad for this team and season already exists.'])->withInput();
        }

        $squad = Squad::findOrFail($id);
        $squad->update($validated);

        return redirect()->route('squads.index')->with('success', 'Squad updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $squad = Squad::findOrFail($id);
        $squad->delete();
        return redirect()->route('squads.index')->with('success', 'Squad deleted successfully.');
    }
}