<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // Importamos SoftDeletes

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    // 1. Definimos el nombre real de la tabla (Laravel busca 'users' por defecto)
    protected $table = 'user';

    // 2. Definimos tu llave primaria personalizada (Laravel busca 'id' por defecto)
    protected $primaryKey = 'user_id';

    // 3. Indicamos los campos que se pueden llenar masivamente (Mass Assignment)
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'role',       // 'Administrador' o 'Usuario'
        'is_active',
        'is_superuser', // Para tu lógica de activado/desactivado
        'is_staff',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // 4. Ocultamos campos sensibles al convertir el usuario a JSON/Array
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 5. Casteo de atributos (convertir datos automáticamente)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel encriptará automáticamente la clave
            'is_active' => 'boolean',
            'is_staff' => 'boolean',
            'is_superuser' => 'boolean',
        ];
    }

    // --- MÉTODOS AUXILIARES (Humanización) ---

    // Obtener nombre completo automáticamente
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Verificar si es Admin (para usarlo fácilmente en el código)
    public function isAdmin()
    {
        // Asumiendo que en tu BD el rol se guarda como texto 'Administrador' o 'admin'
        // Ajusta el string según los datos reales de tu tabla
        return strtolower($this->role) === 'administrador' || $this->is_superuser;
    }
}