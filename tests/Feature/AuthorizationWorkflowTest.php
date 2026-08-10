<?php

use App\Enums\ApprovalStatus;
use App\Livewire\CollegeManagement;
use App\Livewire\RolePermissionManagement;
use App\Livewire\TeacherManagement;
use App\Models\College;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role as PermissionRole;

it('defines user mass assignable attributes without redeclaring the fillable property', function () {
    expect((new User)->getFillable())->toEqualCanonicalizing([
        'name', 'email', 'password', 'college_id', 'mobile_no', 'picture',
        'digital_signature', 'approval_status', 'approved_by', 'approved_at', 'locale',
    ])->and(Schema::hasColumn('users', 'role'))->toBeFalse();
});

it('allows an admin to approve a principal submitted college', function () {
    $admin = User::factory()->withRole('admin')->create();
    $principal = User::factory()->withRole('principal')->create();
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
    $admin = User::factory()->withRole('admin')->create();
    $college = College::query()->create(['name' => 'Principal College', 'approval_status' => ApprovalStatus::Approved]);
    $otherCollege = College::query()->create(['name' => 'Other College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->withRole('teacher')->create();
    $teacher = Teacher::query()->create(['name' => 'Principal Teacher', 'user_id' => $principal->id, 'college_id' => $college->id, 'approval_status' => ApprovalStatus::Approved]);

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('changeRole', $principal->id, 'principal')
        ->assertHasNoErrors();

    $principal->refresh();
    expect($principal->hasRole('principal'))->toBeTrue()
        ->and($principal->teacherProfile?->is($teacher))->toBeTrue()
        ->and($principal->approved_by)->toBe($admin->id);
    $this->actingAs($principal)->get(route('colleges.edit', $college))->assertSuccessful();
    $this->actingAs($principal)->get(route('teachers.edit', $teacher))->assertSuccessful()
        ->assertSee('শিক্ষক প্রোফাইল সম্পাদনা')
        ->assertSee('আমার প্রোফাইল')
        ->assertSee('কলেজ প্রোফাইল');
    $this->actingAs($principal)->get(route('colleges.edit', $otherCollege))->assertForbidden();
    $this->actingAs($principal)->get(route('colleges.create'))->assertForbidden();
});

it('allows admin to change a linked teachers role from teacher management', function () {
    $admin = User::factory()->withRole('admin')->create();
    $college = College::query()->create(['name' => 'Role College', 'approval_status' => ApprovalStatus::Approved]);
    $existingPrincipal = User::factory()->withRole('principal')->create(['college_id' => $college->id]);
    $college->update(['submitted_by' => $existingPrincipal->id]);
    $teacherUser = User::factory()->withRole('teacher')->create(['college_id' => $college->id]);
    $teacher = Teacher::query()->create([
        'name' => 'Role Teacher',
        'user_id' => $teacherUser->id,
        'college_id' => $college->id,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    Livewire::actingAs($admin)->test(TeacherManagement::class)
        ->call('changeTeacherRole', $teacher->id, 'principal')
        ->assertHasNoErrors();

    expect($teacherUser->refresh()->hasRole('principal'))->toBeTrue()
        ->and($teacherUser->hasRole('principal'))->toBeTrue()
        ->and($teacherUser->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacherUser->approved_by)->toBe($admin->id)
        ->and($existingPrincipal->refresh()->hasRole('teacher'))->toBeTrue()
        ->and($existingPrincipal->hasRole('teacher'))->toBeTrue()
        ->and($college->refresh()->submitted_by)->toBe($teacherUser->id);
});

it('allows admin to configure permissions for each role', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->set('selectedRole', 'teacher')
        ->set('selectedPermissions', ['teachers.view', 'teachers.update'])
        ->call('saveRolePermissions')
        ->assertHasNoErrors();

    expect(PermissionRole::findByName('teacher')->permissions->pluck('name')->all())
        ->toEqualCanonicalizing(['teachers.view', 'teachers.update']);
});

it('assigns a custom Spatie role to a user without a legacy role attribute', function () {
    $admin = User::factory()->withRole('admin')->create();
    $user = User::factory()->withRole('teacher')->create();
    PermissionRole::create(['name' => 'content-reviewer', 'guard_name' => 'web']);

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('changeRole', $user->id, 'content-reviewer')
        ->assertHasNoErrors();

    expect($user->refresh()->hasRole('content-reviewer'))->toBeTrue()
        ->and($user->hasRole('teacher'))->toBeFalse()
        ->and(array_key_exists('role', $user->getAttributes()))->toBeFalse();
});

it('allows admin to create rename and delete a custom role', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->set('roleName', 'content-manager')
        ->call('saveRole')
        ->assertHasNoErrors()
        ->assertSet('selectedRole', 'content-manager');

    $customRole = PermissionRole::findByName('content-manager');

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('editRole', $customRole->id)
        ->set('roleName', 'report-manager')
        ->call('saveRole')
        ->assertHasNoErrors()
        ->assertSet('selectedRole', 'report-manager')
        ->call('deleteRole', $customRole->id)
        ->assertHasNoErrors()
        ->assertSet('selectedRole', 'teacher');

    expect(PermissionRole::query()->where('name', 'content-manager')->exists())->toBeFalse()
        ->and(PermissionRole::query()->where('name', 'report-manager')->exists())->toBeFalse();
});

it('protects system roles and custom roles assigned to users from deletion', function () {
    $admin = User::factory()->withRole('admin')->create();
    $customRole = PermissionRole::create(['name' => 'auditor', 'guard_name' => 'web']);
    $user = User::factory()->withRole('teacher')->create();
    $user->assignRole($customRole);

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('deleteRole', $customRole->id)
        ->assertHasErrors('role');

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('deleteRole', PermissionRole::findByName('admin')->id)
        ->assertForbidden();

    expect($customRole->fresh())->not->toBeNull();
});

it('does not promote a user without an approved teacher profile to principal', function () {
    $admin = User::factory()->withRole('admin')->create();
    $user = User::factory()->withRole('teacher')->create();

    Livewire::actingAs($admin)->test(RolePermissionManagement::class)
        ->call('changeRole', $user->id, 'principal')
        ->assertHasErrors('role');

    expect($user->refresh()->hasRole('teacher'))->toBeTrue();
});

it('allows only the college principal or admin to approve a teacher', function () {
    $college = College::query()->create(['name' => 'Approved College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->withRole('principal')->create(['college_id' => $college->id]);
    $otherPrincipal = User::factory()->withRole('principal')->create();
    $teacherUser = User::factory()->withRole('teacher')->create(['college_id' => $college->id]);
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
        ->and($teacherUser->refresh()->teacherProfile?->is($teacher))->toBeTrue();
});

it('restricts administrative pages by role', function () {
    $teacher = User::factory()->withRole('teacher')->create();
    $principal = User::factory()->withRole('principal')->create();

    $this->actingAs($teacher)->get(route('training-catalog.manage'))->assertForbidden();
    $this->actingAs($teacher)->get(route('teachers.manage'))->assertForbidden();
    $this->actingAs($teacher)->get(route('roles-permissions.manage'))->assertForbidden();
    $this->actingAs(User::factory()->withRole('admin')->create())->get(route('roles-permissions.manage'))
        ->assertSuccessful()
        ->assertSee('Roles &amp; Permissions', false)
        ->assertSee('Permission Matrix')
        ->assertSee('Manage roles and permissions')
        ->assertDontSee('রোল ও পারমিশন ব্যবস্থাপনা');
    $this->actingAs($principal)->get('/approvals')->assertNotFound();
});

it('shows college management as a standalone admin navigation item', function () {
    $admin = User::factory()->withRole('admin')->create();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertSuccessful()
        ->assertSeeInOrder(['College Management', 'Roles &amp; Permissions', 'System Settings'], false);
    expect(substr_count($response->getContent(), 'College Management'))->toBe(1);
});

it('lets a teacher access only their approved profile', function () {
    $teacherUser = User::factory()->withRole('teacher')->create();
    $teacher = Teacher::query()->create(['name' => 'Self Service Teacher', 'user_id' => $teacherUser->id, 'approval_status' => ApprovalStatus::Pending]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertForbidden();

    $teacher->update(['approval_status' => ApprovalStatus::Approved]);

    $this->actingAs($teacherUser)->get(route('teachers.show', $teacher))->assertSuccessful();
});
