<?php

namespace App\Models\Organization;

use App\Models\BaseModel;

class KitSponsor extends BaseModel
{
    protected $table = 'kit_sponsor';
    protected $primaryKey = 'id'; // Tu tabla usa 'id' genérico aquí

    protected $fillable = [
        'is_primary', // Si es el patrocinador principal (pecho)
        'placement',  // Manga, Espalda, Pantalón
        'sponsor_id',
        'kit_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    public function kit()
    {
        return $this->belongsTo(TeamKit::class, 'kit_id');
    }
}