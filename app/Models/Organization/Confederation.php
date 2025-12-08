<?php

namespace App\Models\Organization;

use App\Models\BaseModel;

class Confederation extends BaseModel
{
    protected $table = 'confederation';
    protected $primaryKey = 'confederation_id';

    protected $fillable = [
        'name', 
        'acronym', 
        'foundation_year',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}