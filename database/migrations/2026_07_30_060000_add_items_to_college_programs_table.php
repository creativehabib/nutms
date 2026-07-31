<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_programs', function (Blueprint $table): void {
            $table->json('items')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('college_programs', function (Blueprint $table): void {
            $table->dropColumn('items');
        });
    }
};
