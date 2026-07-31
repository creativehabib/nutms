<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use App\Models\Role as PermissionRole;
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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

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
            'role',
            'college_id',
            'teacher_id',
            'approval_status',
            'approved_by',
            'approved_at',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            if (Schema::hasTable('roles') && $user->role !== null) {
                $user->assignRole($user->role->value);
            }
        });

        static::saved(function (User $user): void {
            if ($user->wasChanged('name')) {
                Teacher::query()->where(fn ($query) => $query->whereKey($user->teacher_id)->orWhere('user_id', $user->id))
                    ->update(['name' => $user->name]);
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
            'role' => Role::class,
            'approval_status' => ApprovalStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'model_has_roles', 'model_id', 'role_id')
            ->wherePivot('model_type', self::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function assignRole(string $role): void
    {
        $roleId = PermissionRole::query()->where('name', $role)->value('id');

        if ($roleId !== null) {
            $this->roles()->syncWithoutDetaching([$roleId => ['model_type' => self::class]]);
        }
    }

    /** @param array<int, string> $roles */
    public function syncRoles(array $roles): void
    {
        $roleIds = PermissionRole::query()->whereIn('name', $roles)->pluck('id')
            ->mapWithKeys(fn (int $roleId): array => [$roleId => ['model_type' => self::class]])
            ->all();

        $this->roles()->sync($roleIds);
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->roles()->whereHas('permissions', fn ($query) => $query->where('name', $permission))->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::Admin->value) || $this->role === Role::Admin;
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
