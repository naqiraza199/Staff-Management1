<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftNote extends Model
{
  protected $fillable = ['shift_id', 'note_type', 'note_body', 'keep_private', 'mileage', 'attachments'];
  protected $casts = ['attachments' => 'array'];

}
