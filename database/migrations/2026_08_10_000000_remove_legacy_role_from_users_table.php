<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('teacher')->index();
        });

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            $role = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('model_has_roles.model_type', App\Models\User::class)
                ->where('model_has_roles.model_id', $user->id)
                ->value('roles.name');

            DB::table('users')->where('id', $user->id)->update(['role' => $role ?? 'teacher']);
        });
    }
};
