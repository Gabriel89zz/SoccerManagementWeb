<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Core\Position;

class LineupPlayer extends BaseModel
{
    protected $table = 'lineup_player';
    protected $primaryKey = 'lineup_player_id';

    protected $fillable = [
        'is_starter', 
        'is_captain',
        'match_lineup_id',
        'player_id',
        'position_id', // Posición específica en ese partido
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'is_starter' => 'boolean',
        'is_captain' => 'boolean',
    ];

    public function lineup()
    {
        return $this->belongsTo(MatchLineup::class, 'match_lineup_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}