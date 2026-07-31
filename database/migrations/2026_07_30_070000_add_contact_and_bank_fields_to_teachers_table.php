<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table): void {
            $table->foreignId('division_id')->nullable()->after('employment_id')->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('division_id')->constrained()->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->after('district_id')->constrained()->nullOnDelete();
            $table->text('present_address')->nullable()->after('thana_id');
            $table->text('permanent_address')->nullable()->after('present_address');
            $table->string('bank_name')->nullable()->after('email');
            $table->string('bank_branch_name')->nullable()->after('bank_name');
            $table->string('bank_routing_number', 30)->nullable()->after('bank_branch_name');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('thana_id');
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn(['present_address', 'permanent_address', 'bank_name', 'bank_branch_name', 'bank_routing_number']);
        });
    }
};
