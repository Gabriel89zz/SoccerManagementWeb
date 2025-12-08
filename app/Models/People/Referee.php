<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;

class Referee extends BaseModel
{
    protected $table = 'referee';
    protected $primaryKey = 'referee_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'certification_level', // Ej: 'FIFA', 'Nacional'
        'date_of_birth',
        'country_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // Para obtener el nombre completo fácilmente
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}