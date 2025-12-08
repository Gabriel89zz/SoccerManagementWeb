<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;

class Shot extends BaseModel
{
    protected $table = 'shot';
    protected $primaryKey = 'shot_id';

    protected $fillable = [
        'minute', 
        'is_on_target', 
        'is_goal',
        'location_x', 
        'location_y', 
        'body_part',
        'match_id',
        'team_id',
        'player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'is_on_target' => 'boolean',
        'is_goal' => 'boolean',
    ];
     // Relación con el partido
    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    // Relación con el equipo
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // Relación con el jugador
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}