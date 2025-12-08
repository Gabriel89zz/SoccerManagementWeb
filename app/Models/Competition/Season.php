<?php

namespace App\Models\Competition;

use App\Models\BaseModel;

class Season extends BaseModel
{
    protected $table = 'season';
    protected $primaryKey = 'season_id';

    protected $fillable = [
        'name', 
        'start_date', 
        'end_date',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}