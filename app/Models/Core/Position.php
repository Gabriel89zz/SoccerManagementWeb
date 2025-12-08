<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class Position extends BaseModel
{
    protected $table = 'position';
    protected $primaryKey = 'position_id';

    protected $fillable = [
        'name', 
        'acronym', 
        'category', // 'Goalkeeper', 'Defender', etc.
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}