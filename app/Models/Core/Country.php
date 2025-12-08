<?php

namespace App\Models\Core;

use App\Models\BaseModel;
use App\Models\User;

class Country extends BaseModel
{
    // 1. Apuntamos a la tabla exacta de tu SQL
    protected $table = 'country';
    
    // 2. Definimos la llave primaria correcta
    protected $primaryKey = 'country_id';

    // 3. Campos que permitiremos modificar
    protected $fillable = [
        'name', 
        'iso_code', 
        'confederation_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    // --- RELACIONES ---

    // Un país tiene muchas ciudades
    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
    }

     public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Usuario que actualizó el registro por última vez.
     * Usada en la vista como: $country->editor->username
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación: Usuario que eliminó el registro (Soft Delete).
     * Usada en la vista como: $country->destroyer->username
     */
    public function destroyer()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}