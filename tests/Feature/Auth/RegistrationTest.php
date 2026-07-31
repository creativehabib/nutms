<?php

use Laravel\Fortify\Features;
use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\User;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('public registration always creates a teacher account', function () {
    $college = College::query()->create(['name' => 'Registration College', 'approval_status' => ApprovalStatus::Approved, 'is_active' => true]);

    $this->post(route('register.store'), [
        'name' => 'New Teacher',
        'email' => 'new-teacher@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'principal',
        'college_id' => $college->id,
    ])->assertSessionHasNoErrors();

    $user = User::query()->where('email', 'new-teacher@example.com')->firstOrFail();
    expect($user->role->value)->toBe('teacher')
        ->and($user->college_id)->toBe($college->id);
});

test('registration screen can be rendered', function () {
    College::query()->create(['name' => 'Selectable College', 'approval_status' => ApprovalStatus::Approved, 'is_active' => true]);
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('নতুন account শিক্ষক হিসেবে তৈরি হবে')
        ->assertSee('কলেজের নাম')
        ->assertSee('Selectable College')
        ->assertDontSee('কলেজ প্রিন্সিপাল');
});

test('new users can register', function () {
    $college = College::query()->create(['name' => 'Teacher College', 'approval_status' => ApprovalStatus::Approved, 'is_active' => true]);
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'teacher',
        'college_id' => $college->id,
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(auth()->user()->role->value)->toBe('teacher')
        ->and(auth()->user()->college_id)->toBe($college->id);
});

test('a college is required when a teacher registers', function () {
    $this->post(route('register.store'), [
        'name' => 'Teacher Without College',
        'email' => 'without-college@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('college_id');

    expect(User::query()->where('email', 'without-college@example.com')->exists())->toBeFalse();
});
