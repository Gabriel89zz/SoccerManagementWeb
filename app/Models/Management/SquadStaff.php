<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\StaffMember;

class SquadStaff extends BaseModel
{
    protected $table = 'squad_staff';
    protected $primaryKey = 'squad_staff_id';

    protected $fillable = [
        'start_date', 
        'end_date',
        'squad_id', 
        'staff_member_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function squad()
    {
        return $this->belongsTo(Squad::class, 'squad_id');
    }

    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class, 'staff_member_id');
    }
}