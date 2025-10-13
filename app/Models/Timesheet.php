<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }


    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
