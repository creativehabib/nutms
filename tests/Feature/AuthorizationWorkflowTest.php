<?php

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Livewire\ApprovalManagement;
use App\Models\College;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to approve a principal submitted college', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $principal = User::factory()->create(['role' => UserRole::Principal]);
    $college = College::query()->create([
        'name' => 'Pending College',
        'submitted_by' => $principal->id,
        'approval_status' => ApprovalStatus::Pending,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)->test(ApprovalManagement::class)
        ->call('approveCollege', $college->id)
        ->assertHasNoErrors();

    expect($college->refresh()->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($college->approved_by)->toBe($admin->id)
        ->and($principal->refresh()->college_id)->toBe($college->id);
});

it('allows only the college principal or admin to approve a teacher', function () {
    $college = College::query()->create(['name' => 'Approved College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create(['role' => UserRole::Principal, 'college_id' => $college->id]);
    $otherPrincipal = User::factory()->create(['role' => UserRole::Principal]);
    $teacherUser = User::factory()->create(['role' => UserRole::Teacher, 'college_id' => $college->id]);
    $teacher = Teacher::query()->create([
        'name' => 'Pending Teacher',
        'college_id' => $college->id,
        'user_id' => $teacherUser->id,
        'approval_status' => ApprovalStatus::Pending,
    ]);

    Livewire::actingAs($otherPrincipal)->test(ApprovalManagement::class)
        ->call('approveTeacher', $teacher->id)
        ->assertForbidden();

    Livewire::actingAs($principal)->test(ApprovalManagement::class)
        ->call('approveTeacher', $teacher->id)
        ->assertHasNoErrors();

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->approved_by)->toBe($principal->id)
        ->and($teacherUser->refresh()->teacher_id)->toBe($teacher->id);
});

it('restricts administrative pages by role', function () {
    $teacher = User::factory()->create(['role' => UserRole::Teacher]);
    $principal = User::factory()->create(['role' => UserRole::Principal]);

    $this->actingAs($teacher)->get(route('training-catalog.manage'))->assertForbidden();
    $this->actingAs($teacher)->get(route('teachers.manage'))->assertForbidden();
    $this->actingAs($principal)->get(route('approvals.manage'))->assertSuccessful();
});

it('lets a teacher access only their approved profile', function () {
    $teacherUser = User::factory()->create(['role' => UserRole::Teacher]);
    $teacher = Teacher::query()->create(['name' => 'Self Service Teacher', 'user_id' => $teacherUser->id, 'approval_status' => ApprovalStatus::Pending]);
    $teacherUser->update(['teacher_id' => $teacher->id]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertForbidden();

    $teacher->update(['approval_status' => ApprovalStatus::Approved]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertSuccessful();
});
