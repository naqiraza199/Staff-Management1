<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'client_no',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'display_name',
        'gender',
        'dob',
        'address',
        'unit_or_appartment_no',
        'mobile_number',
        'phone_number',
        'email',
        'religion',
        'marital_status',
        'nationality',
        'languages',
        'pic',
        'is_archive',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'languages' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'use_salutation',
    ];

    /**
     * Get the user that owns the client.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full name of the client.
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        
        $name .= ' ' . $this->last_name;
        
        return $name;
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope a query to only include non-archived clients.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archive', 'Unarchive');
    }

    /**
     * Get the additional contacts for the client.
     */
    public function additionalContacts()
    {
        return $this->hasMany(AdditionalContact::class);
    }

    public function priceBooks()
{
    return $this->belongsToMany(PriceBook::class, 'client_has_price_books')
        ->withPivot('is_default')
        ->withTimestamps();
}

}
