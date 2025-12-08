<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class StaffRole extends BaseModel
{
    protected $table = 'staff_role';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name', // En tu BD es 'role_name'
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}