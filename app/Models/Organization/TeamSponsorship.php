<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\SponsorshipType; // Importante importar este modelo

class TeamSponsorship extends BaseModel
{
    protected $table = 'team_sponsorship';
    protected $primaryKey = 'team_sponsorship_id';

    protected $fillable = [
        'deal_value_eur',
        'sponsor_id',
        'team_id',
        'sponsorship_type_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    protected $casts = [
        'deal_value_eur' => 'decimal:2'
    ];

    // --- RELACIONES ---

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // ESTA ES LA FUNCIÓN QUE FALTABA:
    public function sponsorshipType()
    {
        // El segundo parámetro es la llave foránea en esta tabla ('sponsorship_type_id')
        // El tercer parámetro es la llave primaria en la otra tabla ('sponsorship_type_id')
        return $this->belongsTo(SponsorshipType::class, 'sponsorship_type_id', 'sponsorship_type_id');
    }
}