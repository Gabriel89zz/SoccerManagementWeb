<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class EventType extends BaseModel
{
    protected $table = 'event_type';
    protected $primaryKey = 'event_type_id';

    protected $fillable = [
        'name', 
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}