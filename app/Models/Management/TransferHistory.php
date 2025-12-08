<?php

namespace App\Models\Management;

use App\Models\BaseModel;
use App\Models\People\Player;
use App\Models\Organization\Team;

class TransferHistory extends BaseModel
{
    protected $table = 'transfer_history';
    protected $primaryKey = 'transfer_id';

    protected $fillable = [
        'transfer_date', 
        'transfer_fee_eur', 
        'transfer_type', // 'Transfer', 'Loan', 'Free Agent'
        'player_id',
        'from_team_id',
        'to_team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    // Equipo de origen
    public function fromTeam()
    {
        return $this->belongsTo(Team::class, 'from_team_id');
    }

    // Equipo de destino
    public function toTeam()
    {
        return $this->belongsTo(Team::class, 'to_team_id');
    }
}