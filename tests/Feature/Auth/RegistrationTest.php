<?php

use Laravel\Fortify\Features;
use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\User;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('a principal must select an available approved college', function () {
    $college = College::query()->create(['name' => 'Registration College', 'approval_status' => ApprovalStatus::Approved, 'is_active' => true]);

    $this->post(route('register.store'), [
        'name' => 'College Principal',
        'email' => 'principal@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'principal',
        'college_id' => $college->id,
    ])->assertSessionHasNoErrors();

    expect(User::query()->where('email', 'principal@example.com')->firstOrFail()->college_id)->toBe($college->id);
});

test('the same college cannot be claimed by another principal account', function () {
    $college = College::query()->create(['name' => 'Claimed College', 'approval_status' => ApprovalStatus::Approved, 'is_active' => true]);
    User::factory()->create(['role' => 'principal', 'college_id' => $college->id]);

    $this->post(route('register.store'), [
        'name' => 'Another Principal',
        'email' => 'another-principal@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'principal',
        'college_id' => $college->id,
    ])->assertSessionHasErrors('college_id');
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
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
