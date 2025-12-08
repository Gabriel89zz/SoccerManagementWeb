<?php

namespace App\Models\Competition;

use App\Models\BaseModel;

class CompetitionType extends BaseModel
{
    protected $table = 'competition_type';
    protected $primaryKey = 'type_id'; // Ojo: en tu BD es 'type_id'

    protected $fillable = [
        'type_name', // Ojo: en tu BD es 'type_name'
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}