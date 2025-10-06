<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'company_id',
        'abn',
        'address',
        'phone',
        'payment_terms',
        'contact_email',
        'email_message',
        'payment_rounding',
        'ndia_provider_number',
        'cost_calculation_is_based_on',
        'cancelled_by_client_label',
        'cancel_message',
        'invoice_item_default_format',
        'default_invoice_due_days',
        'invoice_based_on_approved_shift_times',
        'invoice_mileage_based_on_notional_pricing'
    ];

    protected $casts = [
        'invoice_based_on_approved_shift_times' => 'boolean',
        'invoice_mileage_based_on_notional_pricing' => 'boolean',
        'default_invoice_due_days' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
