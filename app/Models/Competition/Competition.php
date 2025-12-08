<?php

namespace App\Models\Competition;

use App\Models\BaseModel;
use App\Models\Core\Country;
use App\Models\Organization\Confederation;

class Competition extends BaseModel
{
    protected $table = 'competition';
    protected $primaryKey = 'competition_id';

    protected $fillable = [
        'name', 
        'confederation_id', 
        'country_id', 
        'competition_type_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    public function type()
    {
        return $this->belongsTo(
            CompetitionType::class, 
            'competition_type_id', // Clave foránea en tabla 'competition'
            'type_id' // Clave primaria en tabla 'competition_type'
        );
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function confederation()
    {
        return $this->belongsTo(Confederation::class, 'confederation_id');
    }
}