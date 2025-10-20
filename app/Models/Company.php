<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Company extends Model
{

    use Billable;
    
    protected $fillable = [
        'company_no',
        'user_id',
        'name',
        'country',
        'staff_invitation_link',
        'company_logo',
        'is_subscribed',
        'subscription_plan_id',
        'quote_title',
        'quote_terms',
        'timezone',
        'minute_interval',
        'pay_run',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
