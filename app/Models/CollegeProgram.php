<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollegeProgram extends Model
{
    protected $fillable = ['level', 'items'];

    protected function casts(): array
    {
        return ['items' => 'array'];
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function getItemNamesAttribute(): array
    {
        if (empty($this->items)) {
            return [];
        }
        if (!is_numeric($this->items[0])) {
            return $this->items;
        }
        if (in_array($this->level, ['degree', 'professional'], true)) {
            return Course::query()->whereIn('id', $this->items)->pluck('name')->toArray();
        }
        return Subject::query()->whereIn('id', $this->items)->pluck('name')->toArray();
    }
}
