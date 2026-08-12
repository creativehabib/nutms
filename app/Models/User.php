<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property string $locale
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that are mass assignable.
     *
     * Defining the allow-list through Eloquent's accessor avoids redeclaring the
     * inherited $fillable property while preserving strict mass-assignment rules.
     *
     * @return array<int, string>
     */
    public function getFillable(): array
    {
        return [
            'name',
            'email',
            'password',
            'college_id',
            'mobile_no',
            'picture',
            'digital_signature',
            'approval_status',
            'approved_by',
            'approved_at',
            'locale',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            $user->assignRole('teacher');
        });

        static::saved(function (User $user): void {
            if ($user->wasChanged('name')) {
                $user->teacherProfile()->update(['name' => $user->name]);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approval_status' => ApprovalStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /** @return BelongsToMany<Training, $this> */
    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class)
            ->withPivot(['status', 'approved_by', 'approved_at', 'completed_at', 'certificate_number'])
            ->withTimestamps();
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPrincipal(): bool
    {
        return $this->hasRole('principal');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function primaryRoleName(): string
    {
        return $this->getRoleNames()->first() ?? 'teacher';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === ApprovalStatus::Approved;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
