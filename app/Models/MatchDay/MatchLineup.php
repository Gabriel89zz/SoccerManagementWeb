<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\Organization\Team;
use App\Models\People\Coach;
use App\Models\Core\Formation;

class MatchLineup extends BaseModel
{
    protected $table = 'match_lineup';
    protected $primaryKey = 'match_lineup_id';

    protected $fillable = [
        'match_id', 
        'team_id', 
        'formation_id', 
        'coach_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'formation_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }
}