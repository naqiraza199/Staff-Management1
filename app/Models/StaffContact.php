<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffContact extends Model
{
     protected $fillable = [
        'user_id',
        'kin_name',
        'kin_relation',
        'kin_contact',
        'kin_email',
        'same_as_kin',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_contact',
        'emergency_contact_email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
