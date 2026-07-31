<?php

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use App\Livewire\CollegeManagement;
use App\Livewire\TeacherManagement;
use App\Models\College;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to approve a principal submitted college', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $principal = User::factory()->create(['role' => Role::Principal]);
    $college = College::query()->create([
        'name' => 'Pending College',
        'submitted_by' => $principal->id,
        'approval_status' => ApprovalStatus::Pending,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)->test(CollegeManagement::class)
        ->call('approveCollege', $college->id)
        ->assertHasNoErrors();

    expect($college->refresh()->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($college->approved_by)->toBe($admin->id)
        ->and($principal->refresh()->college_id)->toBe($college->id);
});

it('requires admin approval before a principal can manage only the selected college', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $college = College::query()->create(['name' => 'Principal College', 'approval_status' => ApprovalStatus::Approved]);
    $otherCollege = College::query()->create(['name' => 'Other College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create([
        'role' => Role::Principal,
        'college_id' => $college->id,
        'approval_status' => ApprovalStatus::Pending,
    ]);

    $this->actingAs($principal)->get(route('colleges.edit', $college))->assertForbidden();

    Livewire::actingAs($admin)->test(CollegeManagement::class)
        ->assertSee('পেন্ডিং')
        ->call('approvePrincipal', $college->id)
        ->assertHasNoErrors();

    $principal->refresh();
    expect($principal->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($principal->approved_by)->toBe($admin->id);
    $this->actingAs($principal)->get(route('colleges.edit', $college))->assertSuccessful();
    $this->actingAs($principal)->get(route('colleges.edit', $otherCollege))->assertForbidden();
    $this->actingAs($principal)->get(route('colleges.create'))->assertForbidden();
});

it('allows only the college principal or admin to approve a teacher', function () {
    $college = College::query()->create(['name' => 'Approved College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create(['role' => Role::Principal, 'college_id' => $college->id]);
    $otherPrincipal = User::factory()->create(['role' => Role::Principal]);
    $teacherUser = User::factory()->create(['role' => Role::Teacher, 'college_id' => $college->id]);
    $teacher = Teacher::query()->create([
        'name' => 'Pending Teacher',
        'college_id' => $college->id,
        'user_id' => $teacherUser->id,
        'approval_status' => ApprovalStatus::Pending,
    ]);

    Livewire::actingAs($otherPrincipal)->test(TeacherManagement::class)
        ->call('approveTeacher', $teacher->id)
        ->assertNotFound();

    Livewire::actingAs($principal)->test(TeacherManagement::class)
        ->assertSee('পেন্ডিং')
        ->call('approveTeacher', $teacher->id)
        ->assertHasNoErrors();

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->approved_by)->toBe($principal->id)
        ->and($teacherUser->refresh()->teacher_id)->toBe($teacher->id);
});

it('restricts administrative pages by role', function () {
    $teacher = User::factory()->create(['role' => Role::Teacher]);
    $principal = User::factory()->create(['role' => Role::Principal]);

    $this->actingAs($teacher)->get(route('training-catalog.manage'))->assertForbidden();
    $this->actingAs($teacher)->get(route('teachers.manage'))->assertForbidden();
    $this->actingAs($principal)->get('/approvals')->assertNotFound();
});

it('lets a teacher access only their approved profile', function () {
    $teacherUser = User::factory()->create(['role' => Role::Teacher]);
    $teacher = Teacher::query()->create(['name' => 'Self Service Teacher', 'user_id' => $teacherUser->id, 'approval_status' => ApprovalStatus::Pending]);
    $teacherUser->update(['teacher_id' => $teacher->id]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertForbidden();

    $teacher->update(['approval_status' => ApprovalStatus::Approved]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertSuccessful();
});
