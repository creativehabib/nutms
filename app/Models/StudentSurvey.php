<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSurvey extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sq1' => 'array',
        'sq2' => 'array',
        'sq5' => 'array',
        'sq6' => 'array',
        'sq7' => 'array',
    ];
}
