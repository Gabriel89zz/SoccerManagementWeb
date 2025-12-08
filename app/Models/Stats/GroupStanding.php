<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\Competition\Group;
use App\Models\Organization\Team;

class GroupStanding extends BaseModel
{
    protected $table = 'group_standing';
    protected $primaryKey = 'group_standing_id';

    protected $fillable = [
        'rank', 
        'played', 
        'won', 
        'drawn', 
        'lost', 
        'goals_for', 
        'goals_against', 
        'goal_difference', 
        'points',
        'group_id', 
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}