<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Challenge extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'prize', 'event_date', 'enroll_limit_date', 'picture', 'prize', 'description', 'status'
    ];
    public function participants(){
        return $this->belongsToMany('App\User', 'App\Models\ChallengesParticipant');
    }
}
