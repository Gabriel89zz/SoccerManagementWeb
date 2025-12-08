<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\Core\EventType;
use App\Models\People\Player;
use App\Models\Organization\Team;

class MatchEvent extends BaseModel
{
    protected $table = 'match_event';
    protected $primaryKey = 'event_id'; // Ojo: en BD es event_id

    protected $fillable = [
        'minute',
        'event_type_id', // Relación con Core/EventType
        'match_id',
        'team_id',
        'player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    
     // Relación con el partido
    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    // Relación con el tipo de evento
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    // Relación con el equipo
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // Relación con el jugador (opcional)
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}