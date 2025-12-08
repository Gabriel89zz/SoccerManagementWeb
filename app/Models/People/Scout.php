<?php

namespace App\Models\People;

use App\Models\BaseModel;
use App\Models\Core\Country;
use App\Models\Organization\Team;

class Scout extends BaseModel
{
    protected $table = 'scout';
    protected $primaryKey = 'scout_id';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'region', // 'South America', 'Europe', etc.
        'date_of_birth',
        'country_id',
        'employing_team_id',
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

    public function employingTeam()
    {
        return $this->belongsTo(Team::class, 'employing_team_id');
    }
}