<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'schedules' ;
    protected $fillable = [
        'event_date', 'event_time', 'places', 'is_online', 'challenge_id'
    ];

}
