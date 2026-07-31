<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('college_programs')->select(['college_id', 'level'])->distinct()
            ->orderBy('college_id')->orderBy('level')
            ->each(function (object $group): void {
                $programs = DB::table('college_programs')
                    ->where('college_id', $group->college_id)
                    ->where('level', $group->level)
                    ->orderBy('id')->get(['id', 'name']);

                $keeper = $programs->first();
                $items = $programs->pluck('name')->filter()->unique()->values()->all();
                DB::table('college_programs')->where('id', $keeper->id)->update([
                    'name' => $items[0],
                    'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                DB::table('college_programs')->whereIn('id', $programs->pluck('id')->skip(1))->delete();
            });
    }

    public function down(): void
    {
        DB::table('college_programs')->whereNotNull('items')->orderBy('id')->each(function (object $program): void {
            $items = json_decode($program->items, true, flags: JSON_THROW_ON_ERROR);
            DB::table('college_programs')->where('id', $program->id)->update(['name' => $items[0]]);
            foreach (array_slice($items, 1) as $item) {
                DB::table('college_programs')->insertOrIgnore([
                    'college_id' => $program->college_id,
                    'level' => $program->level,
                    'name' => $item,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
};
