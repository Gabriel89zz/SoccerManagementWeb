<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;
use App\Models\Organization\Agency;

class Agent extends BaseModel
{
    protected $table = 'agent';
    protected $primaryKey = 'agent_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'license_number',
        'date_of_birth',
        'agency_id',
        'country_id',
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

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}