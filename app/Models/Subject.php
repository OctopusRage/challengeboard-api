<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'subjects';
    protected $fillable = [
        'title', 'review', 'file', 'challenge_id'
    ];

}
