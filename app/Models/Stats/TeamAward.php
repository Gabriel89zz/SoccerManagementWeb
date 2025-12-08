<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\Core\Award;
use App\Models\Organization\Team;
use App\Models\Competition\Season;

class TeamAward extends BaseModel
{
    protected $table = 'team_award';
    protected $primaryKey = 'team_award_id';

    protected $fillable = [
        'award_id', 
        'team_id', 
        'season_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function award()
    {
        return $this->belongsTo(Award::class, 'award_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
}