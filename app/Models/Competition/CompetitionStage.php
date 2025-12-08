<?php

namespace App\Models\Competition;

use App\Models\BaseModel;

class CompetitionStage extends BaseModel
{
    // Respetamos el error tipográfico de la base de datos:
    protected $table = 'competiton_stage'; 
    protected $primaryKey = 'stage_id';

    protected $fillable = [
        'name', 
        'stage_order',
        'competition_season_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function competitionSeason()
    {
        return $this->belongsTo(CompetitionSeason::class, 'competition_season_id');
    }
}