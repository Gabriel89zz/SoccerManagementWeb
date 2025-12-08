<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class Award extends BaseModel
{
    protected $table = 'award';
    protected $primaryKey = 'award_id';

    protected $fillable = [
        'name', 
        'scope', // Campo extra en la BD
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}