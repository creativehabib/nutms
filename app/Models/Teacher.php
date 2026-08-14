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
use RuntimeException;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher_profiles';

    protected $fillable = [
        'user_id',
        'college_id',
        'ttis_id',
        'name',
        'birth_date',
        'designation_id',
        'subject_id',
        'teacher_level_id',
        'employment_id',
        'division_id',
        'district_id',
        'thana_id',
        'present_address',
        'permanent_address',
        'bank_name',
        'bank_branch_name',
        'bank_account_number',
        'bank_routing_number',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return ['approval_status' => ApprovalStatus::class, 'approved_at' => 'datetime', 'birth_date' => 'date'];
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
        static::creating(function (Teacher $teacher): void {
            if (blank($teacher->ttis_id)) {
                $teacher->ttis_id = self::generateUniqueTtisId();
            }
        });

        static::saved(function (Teacher $teacher): void {
            if ($teacher->user_id !== null && ($teacher->wasRecentlyCreated || $teacher->wasChanged(['name', 'college_id']))) {
                User::query()->find($teacher->user_id)?->updateQuietly([
                    'name' => $teacher->name,
                    'college_id' => $teacher->college_id,
                ]);
            }
        });
    }

    public static function generateUniqueTtisId(): string
    {
        $usedTtisIds = self::withTrashed()
            ->pluck('ttis_id')
            ->filter(fn (string $ttisId): bool => preg_match('/^\d{4}$/', $ttisId) === 1)
            ->mapWithKeys(fn (string $ttisId): array => [$ttisId => true])
            ->all();

        for ($candidate = 1000; $candidate <= 9999; $candidate++) {
            $ttisId = (string) $candidate;

            if (! isset($usedTtisIds[$ttisId])) {
                return $ttisId;
            }
        }

        throw new RuntimeException('Unable to generate a TTIS ID because all four-digit IDs are already in use.');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function teacherLevel(): BelongsTo
    {
        return $this->belongsTo(TeacherLevel::class, 'teacher_level_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_id');
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
