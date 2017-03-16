<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChallengesParticipant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id', 'challenges_id', 'status'
    ];

    public function user() {
        $this->belongsTo('App\User')
    }
}
