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
}
