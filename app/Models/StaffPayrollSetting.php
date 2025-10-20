<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffPayrollSetting extends Model
{
  use HasFactory;

    protected $guarded = [];

  public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payGroup()
    {
        return $this->belongsTo(PayGroup::class);
    }
}
