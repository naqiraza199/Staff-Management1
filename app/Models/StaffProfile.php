<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'mobile_number',
        'phone_number',
        'role_type',
        'role_id',
        'gender',
        'dob',
        'employment_type',
        'address',
        'profile_pic',
        'company_id',
        'is_archive',
        'about'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
