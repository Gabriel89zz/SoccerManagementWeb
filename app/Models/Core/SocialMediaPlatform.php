<?php

namespace App\Models\Core;

use App\Models\BaseModel;

class SocialMediaPlatform extends BaseModel
{
    // Nombre de la tabla en tu base de datos
    protected $table = 'social_media_platform';
    
    // Llave primaria (es larga, así que hay que definirla bien)
    protected $primaryKey = 'social_media_platform_id';

    // Campos que permitimos llenar
    protected $fillable = [
        'name', 
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];
}