<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $fillable = [
        'name',
        'status',
        'is_staff_doc',
        'is_competencies',
        'is_qualifications',
        'is_compliance',
        'is_kpi',
        'is_other',
    ];
}       
