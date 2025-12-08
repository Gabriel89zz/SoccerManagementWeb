<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;
use App\Models\Core\Position;

class Player extends BaseModel
{
    protected $table = 'player';
    protected $primaryKey = 'player_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'date_of_birth',
        'preferred_foot', // 'Left', 'Right', 'Both'
        'height', 
        'weight',
        'country_id', 
        'primary_position_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- HUMANIZACIÓN ---
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // --- RELACIONES ---
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'primary_position_id');
    }
}