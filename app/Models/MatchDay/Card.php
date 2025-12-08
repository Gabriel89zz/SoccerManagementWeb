<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;

class Card extends BaseModel
{
    protected $table = 'card';
    protected $primaryKey = 'card_id';

    protected $fillable = [
        'minute', 
        'card_type', // 'Yellow', 'Red'
        'reason',
        'match_id',
        'team_id',
        'player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}