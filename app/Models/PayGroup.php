<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayGroup extends Model
{
    protected $guarded = [];

        public function payGroupDetails()
    {
        return $this->hasMany(PayGroupDetail::class);
    }
}
