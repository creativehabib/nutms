<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class College extends Model
{
    protected $fillable = [
        'code', 'name', 'division_id', 'district_id', 'thana_id', 'address',
        'principal_name', 'college_type', 'has_computer_lab', 'lab_equipment_type',
        'desktop_count', 'laptop_count', 'is_active', 'submitted_by', 'approval_status', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'has_computer_lab' => 'boolean',
            'desktop_count' => 'integer',
            'laptop_count' => 'integer',
            'is_active' => 'boolean',
            'approval_status' => ApprovalStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function principal(): HasOne
    {
        return $this->hasOne(User::class)->where('role', Role::Principal->value);
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

    public function programs(): HasMany
    {
        return $this->hasMany(CollegeProgram::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
