<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimesheetReport extends Model
{
    protected $guarded = [];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function shift()
    {
      return $this->belongsTo(Shift::class);
    }

    protected $casts = [
    'clients' => 'array',
    'allowances' => 'array',
];

}
