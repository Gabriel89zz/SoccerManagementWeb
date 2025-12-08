<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class SponsorshipType extends BaseModel
{
    protected $table = 'sponsorship_type';
    protected $primaryKey = 'sponsorship_type_id';

    protected $fillable = [
        'type_name', // Ojo: en tu BD es 'type_name'
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}