<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Challenges extends Model implements Eloquent
{
    use Authenticatable, Authorizable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'prize', 'event_date', 'enroll_limit_date', 'picture', 'prize', 'description', 'status'
    ];
}
