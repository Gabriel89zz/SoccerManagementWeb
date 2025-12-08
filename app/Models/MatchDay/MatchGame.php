<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\Organization\Team;
use App\Models\Organization\Stadium;
use App\Models\Competition\CompetitionSeason;
use App\Models\Competition\CompetitionStage;

class MatchGame extends BaseModel
{
    // Usamos la tabla 'match' aunque la clase se llame MatchGame
    protected $table = 'match'; 
    protected $primaryKey = 'match_id';

    protected $fillable = [
        'match_date', 
        'home_score', 
        'away_score', 
        'match_status', // 'Scheduled', 'Finished', 'Live'
        'attendance',
        'home_team_id', 
        'away_team_id', 
        'stadium_id',
        'competition_season_id', 
        'stage_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    // --- RELACIONES ---

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }

    public function stage()
    {
        return $this->belongsTo(CompetitionStage::class, 'stage_id'); // Recuerda: tabla competiton_stage
    }

    
}