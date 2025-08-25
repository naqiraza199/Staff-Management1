<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'salutation',
        'first_name',
        'last_name',
        'email',
        'address',
        'unit_or_appartment_no',
        'phone_number',
        'mobile_number',
        'relation',
        'company_name',
        'company_number',
        'purchase_order',
        'reference_number',
        'custom_field',
        'primary_contact',
        'billing_contact',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'primary_contact' => 'boolean',
        'billing_contact' => 'boolean',
    ];

    /**
     * Get the user that owns the additional contact.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client that owns the additional contact.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the full name of the additional contact.
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        
        if ($this->salutation) {
            $name = $this->salutation . ' ' . $name;
        }
        
        $name .= ' ' . $this->last_name;
        
        return $name;
    }

    /**
     * Scope a query to only include primary contacts.
     */
    public function scopePrimaryContacts($query)
    {
        return $query->where('primary_contact', true);
    }

    /**
     * Scope a query to only include billing contacts.
     */
    public function scopeBillingContacts($query)
    {
        return $query->where('billing_contact', true);
    }
}
