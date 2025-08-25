<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamHasStaff extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
    ];
}
