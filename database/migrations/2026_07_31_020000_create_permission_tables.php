<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        $now = now();
        $permissionIds = [];
        foreach (array_keys(config('role-permissions.permissions')) as $permissionName) {
            $permissionIds[$permissionName] = DB::table('permissions')->insertGetId([
                'name' => $permissionName, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $roleIds = [];
        foreach (array_keys(config('role-permissions.defaults')) as $roleName) {
            $roleIds[$roleName] = DB::table('roles')->insertGetId([
                'name' => $roleName, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $configuredPermissions = config("role-permissions.defaults.{$roleName}");
            $permissionNames = $configuredPermissions === ['*'] ? array_keys($permissionIds) : $configuredPermissions;
            foreach ($permissionNames as $permissionName) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionIds[$permissionName], 'role_id' => $roleIds[$roleName],
                ]);
            }
        }

        DB::table('users')->select(['id', 'role'])->orderBy('id')->each(function (object $user) use ($roleIds): void {
            DB::table('model_has_roles')->insert([
                'role_id' => $roleIds[$user->role] ?? $roleIds['teacher'],
                'model_type' => App\Models\User::class,
                'model_id' => $user->id,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
