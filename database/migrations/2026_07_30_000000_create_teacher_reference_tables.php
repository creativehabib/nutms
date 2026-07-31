<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('teachers', function (Blueprint $table): void {
            $table->foreignId('subject_id')->nullable()->after('subject')->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->after('designation')->constrained()->nullOnDelete();
            $table->foreignId('college_id')->nullable()->after('college_name')->constrained()->nullOnDelete();
            $table->foreignId('teacher_level_id')->nullable()->after('teacher_level')->constrained()->nullOnDelete();
            $table->foreignId('employment_id')->nullable()->after('employment_type')->constrained()->nullOnDelete();
        });

        $this->preserveAndLinkExistingTeacherData();
    }

    private function preserveAndLinkExistingTeacherData(): void
    {
        $references = [
            'subject' => ['subjects', 'subject_id'],
            'designation' => ['designations', 'designation_id'],
            'teacher_level' => ['teacher_levels', 'teacher_level_id'],
            'employment_type' => ['employments', 'employment_id'],
        ];

        foreach ($references as $legacyColumn => [$tableName, $foreignKey]) {
            DB::table('teachers')->whereNotNull($legacyColumn)->where($legacyColumn, '!=', '')
                ->select($legacyColumn)->distinct()->orderBy($legacyColumn)->each(function (object $teacher) use ($legacyColumn, $tableName): void {
                    DB::table($tableName)->insertOrIgnore(['name' => $teacher->{$legacyColumn}, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
                });

            DB::table('teachers')->whereNotNull($legacyColumn)->orderBy('id')->each(function (object $teacher) use ($legacyColumn, $tableName, $foreignKey): void {
                $referenceId = DB::table($tableName)->where('name', $teacher->{$legacyColumn})->value('id');
                DB::table('teachers')->where('id', $teacher->id)->update([$foreignKey => $referenceId]);
            });
        }

        DB::table('teachers')->where(function ($query): void {
            $query->whereNotNull('college_name')->orWhereNotNull('college_code');
        })->orderBy('id')->each(function (object $teacher): void {
            $collegeId = filled($teacher->college_code)
                ? DB::table('colleges')->where('code', $teacher->college_code)->value('id')
                : DB::table('colleges')->whereNull('code')->where('name', $teacher->college_name)->value('id');
            if ($collegeId === null) {
                $collegeId = DB::table('colleges')->insertGetId([
                    'code' => filled($teacher->college_code) ? $teacher->college_code : null,
                    'name' => filled($teacher->college_name) ? $teacher->college_name : $teacher->college_code,
                    'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            DB::table('teachers')->where('id', $teacher->id)->update(['college_id' => $collegeId]);
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('subject_id');
            $table->dropConstrainedForeignId('designation_id');
            $table->dropConstrainedForeignId('college_id');
            $table->dropConstrainedForeignId('teacher_level_id');
            $table->dropConstrainedForeignId('employment_id');
        });
        Schema::dropIfExists('colleges');
        foreach (['employments', 'teacher_levels', 'designations', 'subjects'] as $tableName) { Schema::dropIfExists($tableName); }
    }
};
