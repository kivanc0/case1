<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;
    protected $table = 'matches';

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'start_time',
        'end_time',
        'skill_level',
        'auto_pairing',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function participatedMatches()
    {
        return $this->belongsToMany(MatchModel::class, 'match_user');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_match', 'match_id', 'user_id');
    }
    public function participants()
    {
        return $this->belongsToMany(User::class, 'match_user', 'match_id', 'user_id')
            ->withPivot(['id', 'created_at', 'updated_at'])
            ->withCount('matches as totalMatches');
    }
    
    public function matches()
    {
        return $this->belongsToMany(MatchModel::class, 'match_user', 'user_id', 'match_id');
    }
}
