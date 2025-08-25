<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'team_has_staffs');
    }
}
