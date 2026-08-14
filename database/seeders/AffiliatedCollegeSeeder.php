<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AffiliatedCollegeSeeder extends Seeder
{
    private const SOURCE_URL = 'https://raw.githubusercontent.com/creativehabib/nu-data/refs/heads/main/affiliated_college.json';

    public function run(): void
    {
        $divisionIdsByName = Division::query()
            ->pluck('id', 'name')
            ->mapWithKeys(fn (int $id, string $name): array => [$this->normalizeLocationName($name) => $id]);
        $districtIdsByDivisionAndName = District::query()
            ->get(['id', 'division_id', 'name'])
            ->mapWithKeys(fn (District $district): array => [
                $this->locationKey($district->division_id, $district->name) => $district->id,
            ]);
        $thanaIdsByDistrictAndName = Thana::query()
            ->get(['id', 'district_id', 'name'])
            ->mapWithKeys(fn (Thana $thana): array => [
                $this->locationKey($thana->district_id, $thana->name) => $thana->id,
            ]);

        $colleges = Http::acceptJson()
            ->timeout(30)
            ->get(self::SOURCE_URL)
            ->throw()
            ->collect()
            ->map(function (array $college) use ($divisionIdsByName, $districtIdsByDivisionAndName, $thanaIdsByDistrictAndName): array {
                $divisionId = $divisionIdsByName->get($this->normalizeLocationName(Arr::get($college, 'div_name')));
                $districtId = $divisionId === null
                    ? null
                    : $districtIdsByDivisionAndName->get($this->locationKey($divisionId, Arr::get($college, 'districts_name')));
                $thanaId = $districtId === null
                    ? null
                    : $thanaIdsByDistrictAndName->get($this->locationKey($districtId, Arr::get($college, 'upazilla')));

                return [
                    'college_code' => $this->normalizeCollegeCode(Arr::get($college, 'college_code')),
                    'name' => Arr::get($college, 'college_name'),
                    'college_email' => Arr::get($college, 'email'),
                    'division_id' => $divisionId,
                    'district_id' => $districtId,
                    'thana_id' => $thanaId,
                    'address' => Arr::get($college, 'address'),
                    'college_type' => $this->collegeType(Arr::get($college, 'col_type')),
                    'is_active' => true,
                    'approval_status' => ApprovalStatus::Approved->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->filter(fn (array $college): bool => filled($college['college_code']) && filled($college['name']))
            ->values();

        $colleges->chunk(500)->each(function (Collection $chunk): void {
            College::query()->upsert(
                $chunk->all(),
                ['college_code'],
                [
                    'name', 'college_email', 'division_id', 'district_id', 'thana_id', 'address',
                    'college_type', 'is_active', 'approval_status', 'updated_at',
                ],
            );
        });
    }

    private function collegeType(mixed $collegeType): ?string
    {
        return match (Str::upper(trim((string) $collegeType))) {
            'Y' => 'government',
            'N' => 'non_government',
            default => null,
        };
    }

    private function locationKey(int $parentId, mixed $name): string
    {
        return $parentId.'|'.$this->normalizeLocationName($name);
    }

    private function normalizeLocationName(mixed $name): string
    {
        return Str::of((string) $name)->squish()->lower()->toString();
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
