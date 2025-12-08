<?php

namespace App\Models\Competition;

use App\Models\BaseModel;

class Group extends BaseModel
{
    protected $table = 'group'; // 'group' es palabra reservada en SQL, pero Laravel lo maneja bien
    protected $primaryKey = 'group_id';

    protected $fillable = [
        'group_name', // En tu BD es 'group_name'
        'qualification_slots',
        'stage_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function stage()
    {
        return $this->belongsTo(CompetitionStage::class, 'stage_id');
    }
}