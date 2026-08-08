<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSurvey extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'q1' => 'array',
        'q2' => 'array',
        'q3' => 'array',
        'q7' => 'array',
        'q8' => 'array',
    ];
}
