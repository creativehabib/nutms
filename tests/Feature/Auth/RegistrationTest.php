<?php

use Laravel\Fortify\Features;
use App\Models\User;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('public registration always creates a teacher account', function () {
    $this->post(route('register.store'), [
        'name' => 'New Teacher',
        'email' => 'new-teacher@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'principal',
        'college_id' => 999,
    ])->assertSessionHasNoErrors();

    $user = User::query()->where('email', 'new-teacher@example.com')->firstOrFail();
    expect($user->role->value)->toBe('teacher')
        ->and($user->college_id)->toBeNull();
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('নতুন account শিক্ষক হিসেবে তৈরি হবে')
        ->assertDontSee('কলেজ প্রিন্সিপাল');
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'teacher',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(auth()->user()->role->value)->toBe('teacher');
});
