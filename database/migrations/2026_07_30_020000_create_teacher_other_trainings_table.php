<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_other_trainings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
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
    }
};
