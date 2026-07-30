<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingType extends Model
{
    protected $fillable = ['training_institute_id', 'name', 'duration_value', 'duration_unit', 'is_active'];

    protected $attributes = ['is_active' => true];

    protected function casts(): array
    {
        return ['duration_value' => 'integer', 'is_active' => 'boolean'];
    }

    public function trainingInstitute(): BelongsTo
    {
        return $this->belongsTo(TrainingInstitute::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_training')
            ->withPivot(['training_year'])
            ->withTimestamps();
    }
}
