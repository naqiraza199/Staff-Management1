<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
    'billing_reports_ids' => 'array',
    'description' => 'array', 
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function additionalContact()
    {
        return $this->belongsTo(Client::class, 'additional_contact_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

}
