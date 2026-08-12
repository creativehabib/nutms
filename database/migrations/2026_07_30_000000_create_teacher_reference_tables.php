<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['subjects', 'designations', 'teacher_levels', 'employments'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        Schema::create('colleges', function (Blueprint $table): void {
            $table->id();
            $table->string('college_code')->nullable()->unique();
            $table->string('name');
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('college_type', 30)->nullable()->index();
            $table->boolean('has_computer_lab')->nullable()->index();
            $table->string('lab_equipment_type', 20)->nullable()->index();
            $table->unsignedInteger('desktop_count')->nullable();
            $table->unsignedInteger('laptop_count')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('teacher')->index();
            $table->foreignId('college_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approval_status')->default('approved')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        Schema::table('colleges', function (Blueprint $table): void {
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_status')->default('approved')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        Schema::create('college_programs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('college_id')->constrained()->cascadeOnDelete();
            $table->string('level', 30)->index();
            $table->string('name');
            $table->json('items')->nullable();
            $table->timestamps();
            $table->unique(['college_id', 'level', 'name']);
        });

        Schema::create('teacher_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('college_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ttis_id')->nullable()->unique();
            $table->string('name')->nullable();
            $table->date('birth_date')->nullable()->index();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->constrained()->nullOnDelete();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_name')->nullable();
            $table->string('bank_account_number', 100)->nullable();
            $table->string('bank_routing_number', 30)->nullable();
            $table->string('approval_status')->default('approved')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('training_institutes', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('training_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('training_institute_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('duration_value')->nullable();
            $table->string('duration_unit', 20)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['training_institute_id', 'name']);
        });

        Schema::create('teacher_training', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->cascadeOnDelete();
            $table->foreignId('training_type_id')->constrained()->restrictOnDelete();
            $table->year('training_year');
            $table->timestamps();
            $table->unique(['teacher_id', 'training_type_id', 'training_year'], 'teacher_training_unique');
        });

        Schema::create('teacher_other_trainings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->cascadeOnDelete();
            $table->foreignId('training_institute_id')->nullable()->constrained()->nullOnDelete();
            $table->string('institute_name')->nullable();
            $table->string('name');
            $table->unsignedSmallInteger('duration_value')->nullable();
            $table->string('duration_unit', 20)->nullable();
            $table->year('training_year');
            $table->timestamps();
            $table->index(['teacher_id', 'training_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_other_trainings');
        Schema::dropIfExists('teacher_training');
        Schema::dropIfExists('training_types');
        Schema::dropIfExists('training_institutes');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('teacher_profiles');
        Schema::dropIfExists('college_programs');

        Schema::table('colleges', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('submitted_by');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('college_id');
            $table->dropColumn(['role', 'approval_status', 'approved_at']);
        });

        Schema::dropIfExists('colleges');

        foreach (['employments', 'teacher_levels', 'designations', 'subjects'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
