<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\City;

class Academy extends BaseModel
{
    protected $table = 'academy';
    protected $primaryKey = 'academy_id';

    protected $fillable = [
        'name', 
        'city_id', 
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}