<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;
use App\Models\MatchDay\MatchGame; // Asegúrate de importar el modelo MatchGame

class Foul extends BaseModel
{
    protected $table = 'foul';
    protected $primaryKey = 'foul_id';

    protected $fillable = [
        'minute', 
        'is_penalty_kick',
        'match_id',
        'fouling_team_id',
        'fouling_player_id',
        'fouled_team_id',
        'fouled_player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // Relación con el jugador que comete la falta
    public function offender()
    {
        return $this->belongsTo(Player::class, 'fouling_player_id');
    }

    // Relación con el jugador que recibe la falta
    public function victim()
    {
        return $this->belongsTo(Player::class, 'fouled_player_id');
    }

      // Relación con el partido
    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    // Relación con el equipo que comete la falta
    public function foulingTeam()
    {
        return $this->belongsTo(Team::class, 'fouling_team_id');
    }

    // Relación con el equipo que recibe la falta (si está registrado)
    public function fouledTeam()
    {
        return $this->belongsTo(Team::class, 'fouled_team_id');
    }
}