<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherOtherTraining extends Model
{
    protected $fillable = [
        'teacher_id', 'training_institute_id', 'institute_name', 'name',
        'duration_value', 'duration_unit', 'training_year',
    ];

    protected function casts(): array
    {
        return ['duration_value' => 'integer', 'training_year' => 'integer'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function trainingInstitute(): BelongsTo
    {
        return $this->belongsTo(TrainingInstitute::class);
    }
}
