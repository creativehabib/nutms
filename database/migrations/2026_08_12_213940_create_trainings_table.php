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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();

            // বেসিক ইনফরমেশন
            $table->string('title');
            $table->string('slug')->unique()->nullable(); // এসইও বা সুন্দর ইউআরএল এর জন্য
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable(); // ড্যাশবোর্ডে কার্ডে দেখানোর জন্য কভার ছবি

            // সময় এবং শিডিউল
            $table->dateTime('start_date');
            $table->dateTime('end_date'); // ক্যালেন্ডারে ইভেন্ট রেঞ্জ দেখানোর জন্য এটি অত্যাবশ্যক
            $table->dateTime('registration_deadline')->nullable(); // এনরোলমেন্টের শেষ সময়

            // টাইপ ও লোকেশন
            $table->enum('type', ['Online', 'Offline', 'Hybrid'])->default('Offline');
            $table->string('location_or_link')->nullable();

            // ট্রেইনার এবং পার্টিসিপেন্ট
            $table->string('instructor_name')->nullable(); // কে ট্রেনিং করাবেন
            $table->integer('capacity')->nullable(); // কতজন অংশ নিতে পারবেন (সিট লিমিট)

            // স্ট্যাটাস এবং সেটিংস
            $table->boolean('has_certificate')->default(false); // ট্রেনিং শেষে ই-সার্টিফিকেট আছে কি না
            $table->enum('status', ['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'])->default('Draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
