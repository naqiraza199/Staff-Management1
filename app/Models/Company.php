<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_no',
        'user_id',
        'name',
        'country',
        'staff_invitation_link',
        'company_logo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
