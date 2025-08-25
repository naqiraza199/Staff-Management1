<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceBookDetail extends Model
{
    protected $guarded = [];

            protected $casts = [
                'start_time' => 'datetime:H:i',
                'end_time' => 'datetime:H:i',
                'effective_date' => 'date:Y-m-d',
            ];


    public function priceBook()
    {
        return $this->belongsTo(PriceBook::class);
    }
}
