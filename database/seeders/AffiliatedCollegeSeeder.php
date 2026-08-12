<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AffiliatedCollegeSeeder extends Seeder
{
    private const SOURCE_URL = 'https://raw.githubusercontent.com/creativehabib/nu-data/refs/heads/main/affiliated_college.json';

    public function run(): void
    {
        $colleges = Http::acceptJson()
            ->timeout(30)
            ->get(self::SOURCE_URL)
            ->throw()
            ->collect()
            ->map(fn (array $college): array => [
                'college_code' => $this->normalizeCollegeCode(Arr::get($college, 'college_code')),
                'name' => Arr::get($college, 'college_name'),
                'college_email' => Arr::get($college, 'email'),
                'is_active' => true,
                'approval_status' => ApprovalStatus::Approved->value,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->filter(fn (array $college): bool => filled($college['college_code']) && filled($college['name']))
            ->values();

        $colleges->chunk(500)->each(function (Collection $chunk): void {
            DB::table('colleges')->upsert(
                $chunk->all(),
                ['college_code'],
                ['name', 'college_email', 'is_active', 'approval_status', 'updated_at'],
            );
        });
    }

    private function normalizeCollegeCode(mixed $collegeCode): ?string
    {
        $collegeCode = trim((string) $collegeCode);

        if ($collegeCode === '') {
            return null;
        }

        $normalizedCode = ltrim($collegeCode, '0');

        return $normalizedCode === '' ? '0' : $normalizedCode;
    }
}
