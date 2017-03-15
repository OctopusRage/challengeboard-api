<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChallengesTeacher extends Pivot
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
