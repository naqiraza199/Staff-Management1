<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientHasPriceBook extends Model
{
        protected $fillable = [
        'client_id',
        'price_book_id',
        'is_default',
    ];
}
