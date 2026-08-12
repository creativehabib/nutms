<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug', 30)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('program_levels')->insert([
            ['name' => 'Degree', 'slug' => 'degree', 'sort_order' => 10, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Honours', 'slug' => 'honours', 'sort_order' => 20, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Masters', 'slug' => 'masters', 'sort_order' => 30, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Professional', 'slug' => 'professional', 'sort_order' => 40, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Other', 'slug' => 'other', 'sort_order' => 50, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('program_levels');
    }
};
