<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as PermissionRole;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedRole = 'teacher';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public string $roleName = '';

    public ?int $editingRoleId = null;

    public function mount(): void
    {
        $this->loadRolePermissions();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function changeRole(int $userId, string $role): void
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);
        abort_if($userId === auth()->id(), 422, __('You cannot change your own admin role.'));

        $validated = validator(['role' => $role], [
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ])->validate();
        $newRole = $validated['role'];

        DB::transaction(function () use ($userId, $newRole): void {
            $user = User::query()->with(['teacherProfile', 'college'])->lockForUpdate()->findOrFail($userId);
            $wasPrincipal = $user->hasRole('principal');

            if ($newRole === 'principal') {
                $teacher = $user->teacherProfile;
                if ($teacher === null || $teacher->college_id === null || $teacher->approval_status !== ApprovalStatus::Approved) {
                    throw ValidationException::withMessages(['role' => __('Only a user with an approved teacher profile and college can become a Principal.')]);
                }
                $alreadyAssigned = User::query()->where('id', '!=', $user->id)->role('principal')
                    ->where('college_id', $teacher->college_id)->exists();
                if ($alreadyAssigned) {
                    throw ValidationException::withMessages(['role' => __('This college already has a Principal.')]);
                }
                $user->college_id = $teacher->college_id;
                $user->approval_status = ApprovalStatus::Approved;
                $user->approved_by = auth()->id();
                $user->approved_at = now();
                College::query()->whereKey($teacher->college_id)->update(['submitted_by' => $user->id]);
            } elseif ($wasPrincipal) {
                College::query()->where('submitted_by', $user->id)->update(['submitted_by' => null]);
            }

            $user->save();
            $user->syncRoles([$newRole]);
        });

        Flux::toast(variant: 'success', text: __('User role has been updated successfully.'));
    }

    public function updatedSelectedRole(): void
    {
        $this->loadRolePermissions();
    }

    public function createRole(): void
    {
        $this->authorizeRoleManagement();
        $this->resetRoleForm();
        Flux::modal('role-form')->show();
    }

    public function editRole(int $roleId): void
    {
        $this->authorizeRoleManagement();

        $role = PermissionRole::query()->findOrFail($roleId);
        abort_if($this->isSystemRole($role->name), 403, __('System roles cannot be renamed.'));

        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;
        $this->resetValidation();
        Flux::modal('role-form')->show();
    }

    public function saveRole(): void
    {
        $this->authorizeRoleManagement();

        $validated = $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($this->editingRoleId),
            ],
        ], [
            'roleName.regex' => __('Role names may only contain lowercase letters, numbers, hyphens, and underscores.'),
        ]);

        if ($this->editingRoleId === null) {
            $role = PermissionRole::create(['name' => $validated['roleName'], 'guard_name' => 'web']);
            $message = __('Role created successfully.');
        } else {
            $role = PermissionRole::query()->findOrFail($this->editingRoleId);
            abort_if($this->isSystemRole($role->name), 403, __('System roles cannot be renamed.'));
            $role->update(['name' => $validated['roleName']]);
            $message = __('Role updated successfully.');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->selectedRole = $role->name;
        $this->loadRolePermissions();
        $this->resetRoleForm();
        Flux::modal('role-form')->close();
        Flux::toast(variant: 'success', text: $message);
    }

    public function deleteRole(int $roleId): void
    {
        $this->authorizeRoleManagement();

        $role = PermissionRole::query()->findOrFail($roleId);
        abort_if($this->isSystemRole($role->name), 403, __('System roles cannot be deleted.'));

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => __('Remove this role from all users before deleting it.'),
            ]);
        }

        $wasSelected = $this->selectedRole === $role->name;
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($wasSelected) {
            $this->selectedRole = 'teacher';
            $this->loadRolePermissions();
        }

        Flux::toast(variant: 'success', text: __('Role deleted successfully.'));
    }

    public function cancelRoleForm(): void
    {
        $this->resetRoleForm();
    }

    public function saveRolePermissions(): void
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);

        $validated = validator(
            ['role' => $this->selectedRole, 'permissions' => $this->selectedPermissions],
            ['role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')], 'permissions' => ['array'], 'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')]],
        )->validate();

        if ($validated['role'] === 'admin' && ! in_array('roles.manage', $validated['permissions'], true)) {
            throw ValidationException::withMessages(['permissions' => __('The role management permission cannot be removed from the Admin role.')]);
        }

        PermissionRole::findByName($validated['role'])->syncPermissions($validated['permissions']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Flux::toast(variant: 'success', text: __('Role permissions have been saved successfully.'));
    }

    private function loadRolePermissions(): void
    {
        $role = PermissionRole::query()->where('name', $this->selectedRole)->where('guard_name', 'web')->first();
        $this->selectedPermissions = $role?->permissions()->orderBy('name')->pluck('name')->all() ?? [];
    }

    private function authorizeRoleManagement(): void
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);
    }

    private function isSystemRole(string $roleName): bool
    {
        return array_key_exists($roleName, config('role-permissions.defaults'));
    }

    private function resetRoleForm(): void
    {
        $this->roleName = '';
        $this->editingRoleId = null;
        $this->resetValidation('roleName');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);

        return view('livewire.role-permission-management', [
            'users' => User::query()->with(['teacherProfile.college', 'college', 'roles'])
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(12),
            'roles' => PermissionRole::query()->withCount('users')->where('guard_name', 'web')->orderBy('name')->get(),
            'systemRoleNames' => array_keys(config('role-permissions.defaults')),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'permissionLabels' => config('role-permissions.permissions'),
        ])->layout('layouts.app', ['title' => 'Role Management']);
    }
}
