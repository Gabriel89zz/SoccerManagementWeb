<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\Country;

class Sponsor extends BaseModel
{
    protected $table = 'sponsor';
    protected $primaryKey = 'sponsor_id';

    protected $fillable = [
        'name', 
        'industry', 
        'country_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}