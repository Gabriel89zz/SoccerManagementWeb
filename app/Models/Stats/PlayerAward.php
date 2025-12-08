<?php

namespace App\Models\Stats;

use App\Models\BaseModel;
use App\Models\Core\Award;
use App\Models\People\Player;
use App\Models\Competition\Season;

class PlayerAward extends BaseModel
{
    protected $table = 'player_award';
    protected $primaryKey = 'player_award_id';

    protected $fillable = [
        'award_id', 
        'player_id', 
        'season_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function award()
    {
        return $this->belongsTo(Award::class, 'award_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
}