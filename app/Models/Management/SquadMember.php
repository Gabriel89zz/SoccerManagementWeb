<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\Player;

class SquadMember extends BaseModel
{
    protected $table = 'squad_member';
    protected $primaryKey = 'squad_member_id';

    protected $fillable = [
        'jersey_number', 
        'join_date', 
        'leave_date',
        'squad_id',
        'player_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function squad()
    {
        return $this->belongsTo(Squad::class, 'squad_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}