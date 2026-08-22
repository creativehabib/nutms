<?php

use App\Enums\ApprovalStatus;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use Database\Seeders\AffiliatedCollegeSeeder;
use Illuminate\Support\Facades\Http;

it('seeds affiliated colleges from the National University data source', function () {
    $division = Division::query()->create([
        'country_id' => 1,
        'name' => 'Khulna',
        'bn_name' => 'খুলনা',
    ]);
    $district = District::query()->create([
        'division_id' => $division->id,
        'name' => 'Bagerhat',
        'bn_name' => 'বাগেরহাট',
    ]);
    $thana = Thana::query()->create([
        'district_id' => $district->id,
        'name' => 'Bagerhat Sadar',
        'bn_name' => 'বাগেরহাট সদর',
    ]);

    Http::fake([
        'raw.githubusercontent.com/creativehabib/nu-data/*' => Http::response([
            [
                'college_code' => '0101',
                'college_name' => 'GOVT. P. C. COLLEGE',
                'email' => 'c0101@nu.ac.bd',
                'logo' => 'college_logo/101.png',
                'banner' => 'college_banner/101.jpg',
                'id' => 165,
                'role' => 'user',
                'approved' => '1',
                'col_type' => 'Y',
                'address' => 'VILL-HARINKHANA, P.O- P.C. COLLEGE, BAGERHAT',
                'upazilla' => 'BAGERHAT SADAR',
                'districts_name' => 'BAGERHAT',
                'div_name' => 'KHULNA',
            ],
            [
                'college_code' => '0102',
                'college_name' => 'SHERE BANGLA COLLEGE',
                'email' => 'c0102@nu.ac.bd',
                'id' => 978,
                'role' => 'user',
                'approved' => '1',
                'col_type' => 'N',
            ],
        ]),
    ]);

    $this->seed(AffiliatedCollegeSeeder::class);

    $this->assertDatabaseHas('colleges', [
        'college_code' => '101',
        'name' => 'GOVT. P. C. COLLEGE',
        'college_email' => 'c0101@nu.ac.bd',
        'logo' => 'college_logo/101.png',
        'banner' => 'college_banner/101.jpg',
        'division_id' => $division->id,
        'district_id' => $district->id,
        'thana_id' => $thana->id,
        'address' => 'VILL-HARINKHANA, P.O- P.C. COLLEGE, BAGERHAT',
        'college_type' => 'government',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved->value,
    ]);

    $this->assertDatabaseHas('colleges', [
        'college_code' => '102',
        'name' => 'SHERE BANGLA COLLEGE',
        'college_email' => 'c0102@nu.ac.bd',
        'college_type' => 'non_government',
    ]);
});
