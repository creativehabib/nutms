<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table): void {
            $table->foreignId('division_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('division_id')->constrained()->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->after('district_id')->constrained()->nullOnDelete();
            $table->text('address')->nullable()->after('thana_id');
            $table->string('principal_name')->nullable()->after('address');
            $table->string('college_type', 30)->nullable()->after('principal_name')->index();
        });

        Schema::create('college_programs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('college_id')->constrained()->cascadeOnDelete();
            $table->string('level', 30)->index();
            $table->string('name');
            $table->timestamps();
            $table->unique(['college_id', 'level', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_programs');
        Schema::table('colleges', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('thana_id');
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn(['address', 'principal_name', 'college_type']);
        });
    }
};
