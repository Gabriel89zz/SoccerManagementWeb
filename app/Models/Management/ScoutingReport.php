<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\Scout;
use App\Models\People\Player;
use App\Models\MatchDay\MatchGame;

class ScoutingReport extends BaseModel
{
    protected $table = 'scouting_report';
    protected $primaryKey = 'report_id'; // En BD es report_id

    protected $fillable = [
        'report_date', 
        'overall_rating', // Calificación del 1-10 o 1-100
        'summary_text', 
        'scout_id', 
        'scouted_player_id', 
        'match_observed_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'scouted_player_id');
    }

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_observed_id');
    }
}