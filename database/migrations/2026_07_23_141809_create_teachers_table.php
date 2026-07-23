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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('college_code')->nullable()->index();
            $table->string('college_name')->nullable();
            $table->string('tmis_id')->unique()->nullable();
            $table->string('ttis_id')->nullable();
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('subject')->nullable()->index();
            $table->string('teacher_level')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('has_training')->nullable();

            // এই বড় কলামগুলোকে text করে দিন
            $table->text('ict_training_name')->nullable();
            $table->text('ict_training_duration')->nullable();
            $table->text('other_training_name')->nullable();
            $table->text('other_training_duration')->nullable();
            $table->text('training_institute')->nullable();

            $table->string('training_year')->nullable();
            $table->string('has_computer_lab')->nullable();
            $table->integer('computer_count')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
