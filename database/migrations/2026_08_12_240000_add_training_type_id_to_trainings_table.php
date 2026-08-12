<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            $table->foreignId('training_type_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
            $table->boolean('allows_repeat')->default(false)->after('training_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            $table->dropColumn('allows_repeat');
            $table->dropConstrainedForeignId('training_type_id');
        });
    }
};
