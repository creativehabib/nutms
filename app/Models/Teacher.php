<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    // কোনো কলামই প্রোটেক্টেড নয়, সব কলামে ডেটা ইনসার্ট করা যাবে
    protected $guarded = [];

    protected function casts(): array
    {
        return ['approval_status' => ApprovalStatus::class, 'approved_at' => 'datetime'];
    }

    /**
     * Use the linked account as the canonical source of the teacher's name while
     * retaining the legacy column for imported teachers without an account.
     *
     * @return Attribute<string, never>
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(
            fn (): string => (string) ($this->user?->name ?: $this->name),
        );
    }

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

        static::created(function (Teacher $teacher): void {
            if (blank($teacher->ttis_id)) {
                $teacher->updateQuietly([
                    'ttis_id' => self::generateTtisId($teacher->getKey()),
                ]);
            }
        });

        static::saved(function (Teacher $teacher): void {
            if ($teacher->user_id !== null && ($teacher->wasRecentlyCreated || $teacher->wasChanged(['name', 'college_id']))) {
                User::query()->find($teacher->user_id)?->updateQuietly([
                    'name' => $teacher->name,
                    'teacher_id' => $teacher->id,
                    'college_id' => $teacher->college_id,
                ]);
            }
        });
    }

    private static function generateTtisId(int $teacherId): string
    {
        $baseTtisId = 'TTIS-'.str_pad((string) $teacherId, 8, '0', STR_PAD_LEFT);
        $ttisId = $baseTtisId;
        $suffix = 2;

        while (self::withTrashed()->where('ttis_id', $ttisId)->exists()) {
            $ttisId = $baseTtisId.'-'.$suffix;
            $suffix++;
        }

        return $ttisId;
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

    public function trainingTypes(): BelongsToMany
    {
        return $this->belongsToMany(TrainingType::class, 'teacher_training')
            ->withPivot(['training_year'])
            ->withTimestamps();
    }

    public function otherTrainings(): HasMany
    {
        return $this->hasMany(TeacherOtherTraining::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function thana(): BelongsTo
    {
        return $this->belongsTo(Thana::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
