<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->dropColumn('principal_name');
        });
    }

    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->string('principal_name')->nullable()->after('address');
        });
    }
};
