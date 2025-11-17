<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public static bool $skipRoleAssignment = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'image',
        'country',
        'last_login_at',
        'status',
        'job_title_id',
        'private_note',
        'languages',
        'no_access'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'languages' => 'array',
        ];
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function company()
{
    return $this->hasOne(Company::class);
}

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function additionalContacts()
    {
        return $this->hasMany(AdditionalContact::class);
    }

            public function teams()
        {
            return $this->belongsToMany(\App\Models\Team::class, 'team_has_staffs');
        }

}
    