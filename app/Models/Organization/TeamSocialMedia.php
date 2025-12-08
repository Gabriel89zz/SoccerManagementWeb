<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\Core\SocialMediaPlatform;

class TeamSocialMedia extends BaseModel
{
    protected $table = 'team_social_media';
    protected $primaryKey = 'team_social_media_id';

    protected $fillable = [
        'handle', // El usuario (@realmadrid)
        'platform_id',
        'team_id',
        'created_by', 'updated_by', 'deleted_by', 'is_active'
    ];

    public function platform()
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'platform_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}