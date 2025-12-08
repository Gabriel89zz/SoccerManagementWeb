<?php

namespace App\Models\Competition;

use App\Models\BaseModel;

class CompetitionSeason extends BaseModel
{
    protected $table = 'competition_season';
    protected $primaryKey = 'competition_season_id';

    protected $fillable = [
        'competition_id', 
        'season_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
}