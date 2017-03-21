<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Trophy extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'trophies' ;
    protected $fillable = [
        'rank', 'challenge_id', 
    ];

}
