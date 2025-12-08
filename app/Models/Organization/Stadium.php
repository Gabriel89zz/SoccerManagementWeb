<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\City;

class Stadium extends BaseModel
{
    protected $table = 'stadium';
    protected $primaryKey = 'stadium_id';

    protected $fillable = [
        'name', 
        'capacity', 
        'city_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---
    
    // Un estadio está en una ciudad
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}