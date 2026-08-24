<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class College extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'college_code', 'name', 'college_name_bn', 'college_phone', 'male_female', 'total_land', 'establish_year', 'about', 'logo', 'banner', 'eiin', 'division_id', 'district_id', 'thana_id', 'address',
        'college_email', 'college_website', 'college_type', 'has_computer_lab', 'lab_equipment_type',
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

    /** @return array{college: int, slug: string} */
    public function publicProfileRouteParameters(): array
    {
        return [
            'college' => $this->getKey(),
            'slug' => $this->publicProfileSlug(),
        ];
    }

    public function publicProfileSlug(): string
    {
        return Str::slug($this->name) ?: 'college-'.$this->getKey();
    }

    public function publicProfileUrl(): string
    {
        return route('public.colleges.show', $this->publicProfileRouteParameters());
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
        return $this->hasOne(User::class)->whereHas('roles', fn ($query) => $query->where('name', 'principal'));
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

    public function admissionInfos(): HasMany
    {
        return $this->hasMany(AdmissionInfo::class, 'college_code', 'college_code');
    }
}
