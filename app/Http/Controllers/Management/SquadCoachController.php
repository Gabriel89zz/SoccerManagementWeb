<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\SquadCoach;
use App\Models\Management\Squad;
use App\Models\People\Coach;
use Illuminate\Http\Request;

class SquadCoachController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Plantilla (Equipo/Temporada), Coach
        $query = SquadCoach::with(['squad.team', 'squad.season', 'coach']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por nombre del entrenador
                $q->whereHas('coach', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                // O por nombre del equipo
                ->orWhereHas('squad.team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $assignments = $query->orderBy('squad_coach_id', 'desc')->paginate(10);
        $assignments->appends(['search' => $request->search]);

        return view('management.squad_coaches.index', compact('assignments'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar plantillas activas con contexto
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        // Cargar entrenadores activos
        $coaches = Coach::where('is_active', 1)
                        ->orderBy('last_name')
                        ->get();
        
        return view('management.squad_coaches.create', compact('squads', 'coaches'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'coach_id' => 'required|exists:coach,coach_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Validar duplicados
        $exists = SquadCoach::where('squad_id', $request->squad_id)
                            ->where('coach_id', $request->coach_id)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This coach is already assigned to this squad.'])->withInput();
        }

        SquadCoach::create($validated);
        return redirect()->route('squad-coaches.index')->with('success', 'Coach assigned to squad successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $squadCoach = SquadCoach::with(['squad.team', 'squad.season', 'coach'])->findOrFail($id);
        return view('management.squad_coaches.show', compact('squadCoach'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $squadCoach = SquadCoach::findOrFail($id);
        
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        $coaches = Coach::where('is_active', 1)
                        ->orderBy('last_name')
                        ->get();

        return view('management.squad_coaches.edit', compact('squadCoach', 'squads', 'coaches'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'coach_id' => 'required|exists:coach,coach_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Validar duplicados excluyendo actual
        $exists = SquadCoach::where('squad_id', $request->squad_id)
                            ->where('coach_id', $request->coach_id)
                            ->where('squad_coach_id', '!=', $id)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This coach is already assigned to this squad.'])->withInput();
        }

        $squadCoach = SquadCoach::findOrFail($id);
        $squadCoach->update($validated);

        return redirect()->route('squad-coaches.index')->with('success', 'Assignment updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $squadCoach = SquadCoach::findOrFail($id);
        $squadCoach->delete();
        return redirect()->route('squad-coaches.index')->with('success', 'Coach removed from squad successfully.');
    }
}