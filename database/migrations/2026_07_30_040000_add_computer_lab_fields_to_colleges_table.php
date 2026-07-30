<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->boolean('has_computer_lab')->nullable()->after('college_type')->index();
            $table->unsignedInteger('desktop_count')->nullable()->after('has_computer_lab');
            $table->unsignedInteger('laptop_count')->nullable()->after('desktop_count');
        });
    }

    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->dropColumn(['has_computer_lab', 'desktop_count', 'laptop_count']);
        });
    }
};
