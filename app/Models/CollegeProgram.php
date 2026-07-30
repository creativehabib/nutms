<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollegeProgram extends Model
{
    protected $fillable = ['level', 'name'];

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
}
