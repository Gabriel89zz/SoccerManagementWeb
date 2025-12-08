<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\Organization\Team;
use App\Models\MatchDay\MatchGame;

class TeamMatchStat extends BaseModel
{
    protected $table = 'team_match_stat';
    protected $primaryKey = 'team_match_stat_id';

    protected $fillable = [
        'possession_percentage', 
        'corners', 
        'offsides',
        'match_id', 
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'possession_percentage' => 'double', // Asegura decimales correctos
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}