<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayGroupDetail extends Model
{
       protected $guarded = [];

            protected $casts = [
                'start_time' => 'datetime:H:i',
                'end_time' => 'datetime:H:i',
                'effective_date' => 'date:Y-m-d',
            ];


    public function payGroup()
    {
        return $this->belongsTo(PayGroup::class);
    }
}
