<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;

class Coach extends BaseModel
{
    protected $table = 'coach';
    protected $primaryKey = 'coach_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'date_of_birth',
        'license_level', // 'UEFA Pro', 'A License', etc.
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
}