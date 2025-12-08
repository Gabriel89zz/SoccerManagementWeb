<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;
use App\Models\Competition\CompetitionSeason;

class PlayerSeasonStat extends BaseModel
{
    protected $table = 'player_season_stat';
    protected $primaryKey = 'player_season_stat_id';

    protected $fillable = [
        'matches_played', 
        'minutes_played', 
        'goals', 
        'assists', 
        'yellow_cards', 
        'red_cards', 
        'shots_on_target',
        'competition_season_id',
        'player_id', 
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function competitionSeason()
    {
        return $this->belongsTo(CompetitionSeason::class, 'competition_season_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}