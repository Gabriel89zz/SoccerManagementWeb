<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\Coach;

class SquadCoach extends BaseModel
{
    protected $table = 'squad_coach';
    protected $primaryKey = 'squad_coach_id';

    protected $fillable = [
        'start_date', 
        'end_date',
        'squad_id', 
        'coach_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function squad()
    {
        return $this->belongsTo(Squad::class, 'squad_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }
}