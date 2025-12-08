<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;
use App\Models\Core\StaffRole;

class StaffMember extends BaseModel
{
    protected $table = 'staff_member';
    protected $primaryKey = 'staff_member_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'date_of_birth',
        'country_id',
        'role_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function role()
    {
        return $this->belongsTo(StaffRole::class, 'role_id');
    }
}