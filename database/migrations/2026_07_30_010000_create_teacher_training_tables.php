<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_institutes', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true)->index();
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
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_type_id')->constrained()->restrictOnDelete();
            $table->year('training_year');
            $table->timestamps();
            $table->unique(['teacher_id', 'training_type_id', 'training_year'], 'teacher_training_unique');
            $table->index(['training_year', 'training_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_training');
        Schema::dropIfExists('training_types');
        Schema::dropIfExists('training_institutes');
    }
};
