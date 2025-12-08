<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\Organization\Team;
use App\Models\Competition\Season;

class Squad extends BaseModel
{
    protected $table = 'squad';
    protected $primaryKey = 'squad_id';

    protected $fillable = [
        'team_id',
        'season_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
    
    // Relación para acceder a los jugadores de esta plantilla
    public function members()
    {
        return $this->hasMany(SquadMember::class, 'squad_id');
    }
}