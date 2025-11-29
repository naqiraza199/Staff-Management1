<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingReport extends Model
{
    protected $guarded = [];

    public function shift()
    {
      return $this->belongsTo(Shift::class);
    }

        public function client()
    {
      return $this->belongsTo(Client::class);
    }
}
