<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Importamos el modelo User

class BaseModel extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
            $model->is_active = 1;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->updated_by = Auth::id();
                $model->save();
            }
            $model->is_active = 0;
            $model->save();
        });
    }

    // --- RELACIONES DE AUDITORÍA (NUEVO) ---
    // Esto permite usar $team->creator->username en las vistas

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public function destroyer()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }
}