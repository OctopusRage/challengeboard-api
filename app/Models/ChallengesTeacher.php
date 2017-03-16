<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengesTeacher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'challenges_teachers';
    protected $fillable = [
        'users_id','challenges_id'
    ];
    
}
