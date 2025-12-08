<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;

class Substitution extends BaseModel
{
    protected $table = 'substitution';
    protected $primaryKey = 'substitution_id';

    protected $fillable = [
        'minute', 
        'reason',
        'match_id',
        'team_id',
        'player_in_id',
        'player_out_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
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

    // Relación con el jugador que entra
    public function playerIn()
    {
        return $this->belongsTo(Player::class, 'player_in_id');
    }

    // Relación con el jugador que sale
    public function playerOut()
    {
        return $this->belongsTo(Player::class, 'player_out_id');
    }
}