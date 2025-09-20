<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'shift_id',
        'title',
        'body',
        'status',
        'from',
        'note_attachments',
    ];  

    protected $casts = ['note_attachments' => 'array'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
