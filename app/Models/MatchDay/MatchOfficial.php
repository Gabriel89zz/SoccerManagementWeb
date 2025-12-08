<?php

namespace App\Models\MatchDay;

use App\Models\BaseModel;
use App\Models\People\Referee;

class MatchOfficial extends BaseModel
{
    protected $table = 'match_official';
    protected $primaryKey = 'match_official_id';

    protected $fillable = [
        'role', // 'Referee', 'Assistant 1', 'VAR'
        'match_id',
        'referee_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function referee()
    {
        return $this->belongsTo(Referee::class, 'referee_id');
    }
}