<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\Country;

class Agency extends BaseModel
{
    protected $table = 'agency';
    protected $primaryKey = 'agency_id';

    protected $fillable = [
        'name', 
        'country_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}