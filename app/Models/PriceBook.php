<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceBook extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fixed_price' => 'boolean',
        'provider_travel' => 'boolean',
        'national_pricing' => 'boolean',
    ];

    public function priceBookDetails()
    {
        return $this->hasMany(PriceBookDetail::class);
    }
}
