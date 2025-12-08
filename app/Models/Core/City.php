<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class City extends BaseModel
{
    protected $table = 'city';
    protected $primaryKey = 'city_id';

    protected $fillable = [
        'name', 
        'country_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    // Una ciudad pertenece a un país
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}