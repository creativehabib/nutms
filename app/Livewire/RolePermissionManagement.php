<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
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

        $validated = validator(['role' => $role], ['role' => ['required', Rule::enum(Role::class)]])->validate();
        $newRole = Role::from($validated['role']);

        DB::transaction(function () use ($userId, $newRole): void {
            $user = User::query()->with(['teacherProfile', 'college'])->lockForUpdate()->findOrFail($userId);
            $previousRole = $user->role;

            if ($newRole === Role::Principal) {
                $teacher = $user->teacherProfile;
                if ($teacher === null || $teacher->college_id === null || $teacher->approval_status !== ApprovalStatus::Approved) {
                    throw ValidationException::withMessages(['role' => __('Only a user with an approved teacher profile and college can become a Principal.')]);
                }
                $alreadyAssigned = User::query()->where('id', '!=', $user->id)->where('role', Role::Principal->value)
                    ->where('college_id', $teacher->college_id)->exists();
                if ($alreadyAssigned) {
                    throw ValidationException::withMessages(['role' => __('This college already has a Principal.')]);
                }
                $user->college_id = $teacher->college_id;
                $user->approval_status = ApprovalStatus::Approved;
                $user->approved_by = auth()->id();
                $user->approved_at = now();
                College::query()->whereKey($teacher->college_id)->update(['submitted_by' => $user->id]);
            } elseif ($previousRole === Role::Principal) {
                College::query()->where('submitted_by', $user->id)->update(['submitted_by' => null]);
            }

            $user->role = $newRole;
            $user->save();
            $user->syncRoles([$newRole->value]);
        });

        Flux::toast(variant: 'success', text: __('User role has been updated successfully.'));
    }

    public function updatedSelectedRole(): void
    {
        $this->loadRolePermissions();
    }

    public function saveRolePermissions(): void
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);

        $validated = validator(
            ['role' => $this->selectedRole, 'permissions' => $this->selectedPermissions],
            ['role' => ['required', Rule::enum(Role::class)], 'permissions' => ['array'], 'permissions.*' => ['string', Rule::exists('permissions', 'name')]],
        )->validate();

        if ($validated['role'] === Role::Admin->value && ! in_array('roles.manage', $validated['permissions'], true)) {
            throw ValidationException::withMessages(['permissions' => __('The role management permission cannot be removed from the Admin role.')]);
        }

        PermissionRole::findByName($validated['role'])->syncPermissions($validated['permissions']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Flux::toast(variant: 'success', text: __('Role permissions have been saved successfully.'));
    }

    private function loadRolePermissions(): void
    {
        $this->selectedPermissions = PermissionRole::findByName($this->selectedRole)
            ->permissions()->orderBy('name')->pluck('name')->all();
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);

        return view('livewire.role-permission-management', [
            'users' => User::query()->with(['teacherProfile.college', 'college'])
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(12),
            'roles' => Role::cases(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'permissionLabels' => config('role-permissions.permissions'),
        ]);
    }
}
