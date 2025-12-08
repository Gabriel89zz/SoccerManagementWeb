<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Core\InjuryType;
use App\Models\MatchDay\MatchGame; // Para saber en qué partido ocurrió

class Injury extends BaseModel
{
    protected $table = 'injury';
    protected $primaryKey = 'injury_id';

    protected $fillable = [
        'date_incurred', 
        'expected_return_date', 
        'actual_return_date',
        'player_id', 
        'injury_type_id', 
        'match_id_incurred', // ID del partido donde se lesionó
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function injuryType()
    {
        return $this->belongsTo(InjuryType::class, 'injury_type_id');
    }

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id_incurred');
    }
}