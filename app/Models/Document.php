<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = []; // allows all fields


    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

            public function client()
    {
        return $this->belongsTo(Client::class);
    }

        protected $casts = [
        'no_expiration' => 'boolean', // important!
    ];
}
