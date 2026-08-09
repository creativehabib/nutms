<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admission_infos', function (Blueprint $table) {
            $table->id();
            $table->string('college_code')->index();
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('college_name');
            $table->string('subject_id');
            $table->string('subject_name');
            $table->integer('sess_21_22_total_admited')->nullable();
            $table->integer('sess_22_23_total_admited')->nullable();
            $table->integer('sess_23_24_total_admited')->nullable();
            $table->integer('sess_24_25_total_admited')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_infos');
    }
};
