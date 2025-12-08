<?php

namespace App\Models\Competition;

use App\Models\BaseModel;
use App\Models\Organization\Team;
use App\Models\User\User;

class CompetitionSeasonTeam extends BaseModel
{
    protected $table = 'competition_season_team';
    protected $primaryKey = 'competition_season_team_id';

    protected $fillable = [
        'competition_season_id',
        'team_id',
        'final_position',
        'overall_status',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // --- RELACIONES ---

    public function competitionSeason()
    {
        return $this->belongsTo(
            CompetitionSeason::class,
            'competition_season_id',
            'competition_season_id'
        );
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}