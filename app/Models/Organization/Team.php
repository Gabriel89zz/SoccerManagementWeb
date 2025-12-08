<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\Country;

class Team extends BaseModel
{
    protected $table = 'team';
    protected $primaryKey = 'team_id';

    protected $fillable = [
        'name', 
        'short_name', 
        'foundation_date', 
        'country_id', 
        'home_stadium_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    // Un equipo pertenece a un país
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // Un equipo tiene un estadio local (opcional, por eso usamos el operador seguro)
    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'home_stadium_id');
    }
}