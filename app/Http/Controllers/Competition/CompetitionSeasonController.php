<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition\CompetitionSeason;
use App\Models\Competition\Competition;
use App\Models\Competition\Season;
use Illuminate\Http\Request;

class CompetitionSeasonController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading
        $query = CompetitionSeason::with(['competition', 'season']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Buscar por nombre de la competición
                $q->whereHas('competition', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                    // O por nombre de la temporada
                    ->orWhereHas('season', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $competitionSeasons = $query->orderBy('competition_season_id', 'desc')->paginate(10);
        $competitionSeasons->appends(['search' => $request->search]);

        return view('competition.competition_seasons.index', compact('competitionSeasons'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $competitions = Competition::where('is_active', 1)->orderBy('name')->get();
        // Ordenamos temporadas descendente (más reciente primero)
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();

        return view('competition.competition_seasons.create', compact('competitions', 'seasons'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => 'required|exists:competition,competition_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        // Opcional: Validar duplicados (misma comp + misma season)
        $exists = CompetitionSeason::where('competition_id', $request->competition_id)
            ->where('season_id', $request->season_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This competition is already assigned to this season.'])->withInput();
        }

        CompetitionSeason::create($validated);
        return redirect()->route('competition-seasons.index')->with('success', 'Association created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $competitionSeason = CompetitionSeason::with(['competition', 'season'])->findOrFail($id);
        return view('competition.competition_seasons.show', compact('competitionSeason'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $competitionSeason = CompetitionSeason::findOrFail($id);

        $competitions = Competition::where('is_active', 1)->orderBy('name')->get();
        $seasons = Season::where('is_active', 1)->orderBy('name', 'desc')->get();

        return view('competition.competition_seasons.edit', compact('competitionSeason', 'competitions', 'seasons'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'competition_id' => 'required|exists:competition,competition_id',
            'season_id' => 'required|exists:season,season_id',
        ]);

        // Validar duplicados excluyendo el actual
        $exists = CompetitionSeason::where('competition_id', $request->competition_id)
            ->where('season_id', $request->season_id)
            ->where('competition_season_id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This competition is already assigned to this season.'])->withInput();
        }

        $competitionSeason = CompetitionSeason::findOrFail($id);
        $competitionSeason->update($validated);

        return redirect()->route('competition-seasons.index')->with('success', 'Association updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $competitionSeason = CompetitionSeason::findOrFail($id);
        $competitionSeason->delete();
        return redirect()->route('competition-seasons.index')->with('success', 'Association deleted successfully.');
    }
}