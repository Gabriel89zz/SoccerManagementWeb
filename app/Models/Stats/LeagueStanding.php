<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\Competition\CompetitionSeason;
use App\Models\Organization\Team;

class LeagueStanding extends BaseModel
{
    protected $table = 'league_standing';
    protected $primaryKey = 'league_standing_id';

    protected $fillable = [
        'rank', // Posición (1ro, 2do...)
        'played', 
        'won', 
        'drawn', 
        'lost', 
        'goals_for', 
        'goals_against', 
        'goal_difference', 
        'points',
        'competition_season_id', 
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function competitionSeason()
    {
        return $this->belongsTo(CompetitionSeason::class, 'competition_season_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}