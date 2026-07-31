<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $guarded = [];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    /** @param array<int, string> $permissions */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync(Permission::query()->whereIn('name', $permissions)->pluck('id'));
    }
}
