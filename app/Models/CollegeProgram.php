<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollegeProgram extends Model
{
    protected $fillable = ['level', 'name', 'items'];

    protected function casts(): array
    {
        return ['items' => 'array'];
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
}
