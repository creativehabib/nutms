<?php

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use App\Livewire\CollegeManagement;
use App\Livewire\RolePermissionManagement;
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

it('allows admin to promote an approved teacher to principal of their college', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $college = College::query()->create(['name' => 'Principal College', 'approval_status' => ApprovalStatus::Approved]);
    $otherCollege = College::query()->create(['name' => 'Other College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create(['role' => Role::Teacher]);
    $teacher = Teacher::query()->create(['name' => 'Principal Teacher', 'user_id' => $principal->id, 'college_id' => $college->id, 'approval_status' => ApprovalStatus::Approved]);

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('changeRole', $principal->id, Role::Principal->value)
        ->assertHasNoErrors();

    $principal->refresh();
    expect($principal->role)->toBe(Role::Principal)
        ->and($principal->teacher_id)->toBe($teacher->id)
        ->and($principal->approved_by)->toBe($admin->id);
    $this->actingAs($principal)->get(route('colleges.edit', $college))->assertSuccessful();
    $this->actingAs($principal)->get(route('teachers.edit', $teacher))->assertSuccessful()
        ->assertSee('শিক্ষক প্রোফাইল সম্পাদনা')
        ->assertSee('আমার প্রোফাইল')
        ->assertSee('কলেজ প্রোফাইল');
    $this->actingAs($principal)->get(route('colleges.edit', $otherCollege))->assertForbidden();
    $this->actingAs($principal)->get(route('colleges.create'))->assertForbidden();
});

it('does not promote a user without an approved teacher profile to principal', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $user = User::factory()->create(['role' => Role::Teacher]);

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('changeRole', $user->id, Role::Principal->value)
        ->assertHasErrors('role');

    expect($user->refresh()->role)->toBe(Role::Teacher);
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
    $this->actingAs($teacher)->get(route('roles-permissions.manage'))->assertForbidden();
    $this->actingAs(User::factory()->create(['role' => Role::Admin]))->get(route('roles-permissions.manage'))->assertSuccessful();
    $this->actingAs($principal)->get('/approvals')->assertNotFound();
});

it('shows college management as a standalone admin navigation item', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertSuccessful()
        ->assertSeeInOrder(['কলেজ ব্যবস্থাপনা', 'রোলস ও পারমিশন', 'শিক্ষক সেটিংস']);
    expect(substr_count($response->getContent(), 'কলেজ ব্যবস্থাপনা'))->toBe(1);
});

it('lets a teacher access only their approved profile', function () {
    $teacherUser = User::factory()->create(['role' => Role::Teacher]);
    $teacher = Teacher::query()->create(['name' => 'Self Service Teacher', 'user_id' => $teacherUser->id, 'approval_status' => ApprovalStatus::Pending]);
    $teacherUser->update(['teacher_id' => $teacher->id]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertForbidden();

    $teacher->update(['approval_status' => ApprovalStatus::Approved]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertSuccessful();
});
