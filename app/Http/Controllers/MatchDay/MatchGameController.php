<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\MatchGame;
use App\Models\Organization\Team;
use App\Models\Organization\Stadium;
use App\Models\Competition\CompetitionStage;
use Illuminate\Http\Request;

class MatchGameController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = MatchGame::with(['homeTeam', 'awayTeam', 'stadium', 'stage.competitionSeason.competition']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('homeTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('awayTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('stadium', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('stage.competitionSeason.competition', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $matches = $query->orderBy('match_date', 'desc')->paginate(10);
        $matches->appends(['search' => $request->search]);

        return view('match_day.matches.index', compact('matches'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $stadiums = Stadium::where('is_active', 1)->orderBy('name')->get()->unique('name');
        
        $stages = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season'])
                                  ->where('is_active', 1)
                                  ->get();

        return view('match_day.matches.create', compact('teams', 'stadiums', 'stages'));
    }

    // 3. STORE (CORREGIDO: Agregados score y attendance)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_date' => 'required|date',
            'home_team_id' => 'required|exists:team,team_id',
            'away_team_id' => 'required|exists:team,team_id|different:home_team_id',
            'stadium_id' => 'nullable|exists:stadium,stadium_id',
            'stage_id' => 'required|exists:competiton_stage,stage_id', 
            'match_status' => 'required|string|max:50',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'attendance' => 'nullable|integer|min:0',
        ]);

        $stage = CompetitionStage::findOrFail($request->stage_id);
        
        $data = $request->all();
        $data['competition_season_id'] = $stage->competition_season_id;

        MatchGame::create($data);
        return redirect()->route('matches.index')->with('success', 'Match created successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $match = MatchGame::with(['homeTeam', 'awayTeam', 'stadium', 'stage.competitionSeason.competition'])->findOrFail($id);
        return view('match_day.matches.show', compact('match'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $match = MatchGame::findOrFail($id);
        
        $teams = Team::where('is_active', 1)->orderBy('name')->get();
        $stadiums = Stadium::where('is_active', 1)->orderBy('name')->get()->unique('name');
        $stages = CompetitionStage::with(['competitionSeason.competition', 'competitionSeason.season'])
                                  ->where('is_active', 1)
                                  ->get();

        return view('match_day.matches.edit', compact('match', 'teams', 'stadiums', 'stages'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_date' => 'required|date',
            'home_team_id' => 'required|exists:team,team_id',
            'away_team_id' => 'required|exists:team,team_id|different:home_team_id',
            'stadium_id' => 'nullable|exists:stadium,stadium_id',
            'stage_id' => 'required|exists:competiton_stage,stage_id',
            'match_status' => 'required|string|max:50',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'attendance' => 'nullable|integer|min:0',
        ]);

        $match = MatchGame::findOrFail($id);
        
        if($match->stage_id != $request->stage_id) {
             $stage = CompetitionStage::findOrFail($request->stage_id);
             $request->merge(['competition_season_id' => $stage->competition_season_id]);
        }

        $match->update($request->all());

        return redirect()->route('matches.index')->with('success', 'Match updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $match = MatchGame::findOrFail($id);
        $match->delete();
        return redirect()->route('matches.index')->with('success', 'Match deleted successfully.');
    }
}