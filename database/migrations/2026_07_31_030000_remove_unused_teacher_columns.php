<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table): void {
            $table->dropColumn([
                'has_training',
                'ict_training_duration',
                'other_training_duration',
                'training_year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table): void {
            $table->string('has_training')->nullable();
            $table->text('ict_training_duration')->nullable();
            $table->text('other_training_duration')->nullable();
            $table->string('training_year')->nullable();
        });
    }
};
