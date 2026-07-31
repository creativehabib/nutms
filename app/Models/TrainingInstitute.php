<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingInstitute extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $attributes = ['is_active' => true];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function trainingTypes(): HasMany
    {
        return $this->hasMany(TrainingType::class);
    }
}
