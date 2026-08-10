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
        'admin@example.com' => ['mobile' => '01700000001', 'role' => 'admin'],
        'principal@example.com' => ['mobile' => '01700000002', 'role' => 'principal'],
        'teacher@example.com' => ['mobile' => '01700000003', 'role' => 'teacher'],
    ];

    foreach ($expectedUsers as $email => $expectedUser) {
        $user = User::query()->where('email', $email)->firstOrFail();

        expect($user->mobile_no)->toBe($expectedUser['mobile'])
            ->and($user->hasRole($expectedUser['role']))->toBeTrue()
            ->and($user->email_verified_at)->not->toBeNull();
    }
});
