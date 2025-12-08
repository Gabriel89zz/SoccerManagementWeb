<?php

namespace App\Models\Organization;

use App\Models\BaseModel;

class TeamKit extends BaseModel
{
    protected $table = 'team_kit';
    protected $primaryKey = 'kit_id';

    protected $fillable = [
        'kit_type', // Local, Visitante, Tercero
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}