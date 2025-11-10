<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftNote extends Model
{
  protected $fillable = ['shift_id', 'note_type', 'note_body', 'keep_private', 'mileage', 'attachments','user_id','title','client_id','staff_note'];
  protected $casts = ['attachments' => 'array'];
    public function shift()
    {
      return $this->belongsTo(Shift::class);
    }

        public function user()
    {
      return $this->belongsTo(User::class);
    }

   public function client()
{
    return $this->belongsTo(\App\Models\Client::class, 'client_id');
}

}
