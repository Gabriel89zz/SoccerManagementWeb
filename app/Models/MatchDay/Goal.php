<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;

class Goal extends BaseModel
{
    protected $table = 'goal';
    protected $primaryKey = 'goal_id';

    protected $fillable = [
        'minute', 
        'is_own_goal', 
        'is_penalty', 
        'body_part', // 'Head', 'Right Foot'
        'match_id',
        'scoring_team_id',
        'scoring_player_id',
        'assist_player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'is_own_goal' => 'boolean',
        'is_penalty' => 'boolean',
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function scorer()
    {
        return $this->belongsTo(Player::class, 'scoring_player_id');
    }

    public function assistant()
    {
        return $this->belongsTo(Player::class, 'assist_player_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'scoring_team_id');
    }
}