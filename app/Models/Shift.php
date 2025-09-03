<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
      protected $fillable = [
        'client_section',
        'shift_section',
        'time_and_location',
        'add_to_job_board',
        'carer_section',
        'job_section',
        'instruction',
        'company_id',
    ];

    protected $casts = [
        'client_section'     => 'array',
        'shift_section'      => 'array',
        'time_and_location'  => 'array',
        'add_to_job_board'   => 'boolean',
        'carer_section'      => 'array',
        'job_section'        => 'array',
        'instruction'        => 'array',
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class);
    }
}
