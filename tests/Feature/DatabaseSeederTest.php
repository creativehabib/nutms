<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Http;

it('seeds demo users with all required user table attributes', function () {
    Http::fake([
        'raw.githubusercontent.com/creativehabib/nu-data/*' => Http::response([]),
    ]);

    $this->seed(DatabaseSeeder::class);

    $expectedUsers = [
        'admin@example.com' => '01700000001',
        'principal@example.com' => '01700000002',
        'teacher@example.com' => '01700000003',
    ];

    foreach ($expectedUsers as $email => $mobileNumber) {
        $user = User::query()->where('email', $email)->firstOrFail();

        expect($user->mobile_no)->toBe($mobileNumber)
            ->and($user->email_verified_at)->not->toBeNull();
    }
});
