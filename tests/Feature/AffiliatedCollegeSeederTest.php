<?php

use App\Enums\ApprovalStatus;
use Database\Seeders\AffiliatedCollegeSeeder;
use Illuminate\Support\Facades\Http;

it('seeds affiliated colleges from the National University data source', function () {
    Http::fake([
        'raw.githubusercontent.com/creativehabib/nu-data/*' => Http::response([
            [
                'college_code' => '0101',
                'college_name' => 'GOVT. P. C. COLLEGE',
                'email' => 'c0101@nu.ac.bd',
                'id' => 165,
                'role' => 'user',
                'approved' => '1',
            ],
            [
                'college_code' => '0102',
                'college_name' => 'SHERE BANGLA COLLEGE',
                'email' => 'c0102@nu.ac.bd',
                'id' => 978,
                'role' => 'user',
                'approved' => '1',
            ],
        ]),
    ]);

    $this->seed(AffiliatedCollegeSeeder::class);

    $this->assertDatabaseHas('colleges', [
        'college_code' => '101',
        'name' => 'GOVT. P. C. COLLEGE',
        'college_email' => 'c0101@nu.ac.bd',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved->value,
    ]);

    $this->assertDatabaseHas('colleges', [
        'college_code' => '102',
        'name' => 'SHERE BANGLA COLLEGE',
        'college_email' => 'c0102@nu.ac.bd',
    ]);
});
