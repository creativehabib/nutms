<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    // কোনো কলামই প্রোটেক্টেড নয়, সব কলামে ডেটা ইনসার্ট করা যাবে
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Teacher $teacher): void {
            if ($teacher->isDirty('subject')) {
                $teacher->subject_id = filled($teacher->subject) ? Subject::query()->firstOrCreate(['name' => $teacher->subject])->id : null;
            }
            if ($teacher->isDirty('designation')) {
                $teacher->designation_id = filled($teacher->designation) ? Designation::query()->firstOrCreate(['name' => $teacher->designation])->id : null;
            }
            if ($teacher->isDirty('teacher_level')) {
                $teacher->teacher_level_id = filled($teacher->teacher_level) ? TeacherLevel::query()->firstOrCreate(['name' => $teacher->teacher_level])->id : null;
            }
            if ($teacher->isDirty('employment_type')) {
                $teacher->employment_id = filled($teacher->employment_type) ? Employment::query()->firstOrCreate(['name' => $teacher->employment_type])->id : null;
            }

            if (($teacher->isDirty('college_code') || $teacher->isDirty('college_name')) && (filled($teacher->college_code) || filled($teacher->college_name))) {
                $college = College::query()->firstOrCreate(
                    filled($teacher->college_code) ? ['code' => $teacher->college_code] : ['name' => $teacher->college_name],
                    ['name' => $teacher->college_name ?: $teacher->college_code],
                );
                $teacher->college_id = $college->id;
            } elseif ($teacher->isDirty('college_code') || $teacher->isDirty('college_name')) {
                $teacher->college_id = null;
            }
        });
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function teacherLevel(): BelongsTo
    {
        return $this->belongsTo(TeacherLevel::class);
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }
}
