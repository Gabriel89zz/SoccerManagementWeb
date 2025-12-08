<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class Formation extends BaseModel
{
    // Nombre de la tabla en tu base de datos
    protected $table = 'formation';
    
    // Llave primaria
    protected $primaryKey = 'formation_id';

    // Campos que permitimos llenar
    protected $fillable = [
        'name', 
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}