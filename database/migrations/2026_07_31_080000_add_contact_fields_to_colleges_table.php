<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->string('college_email')->nullable()->after('principal_name');
            $table->string('college_website')->nullable()->after('college_email');
        });
    }

    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->dropColumn(['college_email', 'college_website']);
        });
    }
};
