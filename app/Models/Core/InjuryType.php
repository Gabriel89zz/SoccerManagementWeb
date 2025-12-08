<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class InjuryType extends BaseModel
{
    protected $table = 'injury_type';
    protected $primaryKey = 'injury_type_id';

    protected $fillable = [
        'name', 
        'severity_level', // Campo extra que tienes en la BD
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}