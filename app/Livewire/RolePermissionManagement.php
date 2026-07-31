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

class RolePermissionManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function changeRole(int $userId, string $role): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_if($userId === auth()->id(), 422, 'নিজের এডমিন রোল পরিবর্তন করা যাবে না।');

        $validated = validator(['role' => $role], ['role' => ['required', Rule::enum(Role::class)]])->validate();
        $newRole = Role::from($validated['role']);

        DB::transaction(function () use ($userId, $newRole): void {
            $user = User::query()->with(['teacher', 'college'])->lockForUpdate()->findOrFail($userId);
            $previousRole = $user->role;

            if ($newRole === Role::Principal) {
                $teacher = $user->teacher;
                if ($teacher === null || $teacher->college_id === null || $teacher->approval_status !== ApprovalStatus::Approved) {
                    throw ValidationException::withMessages(['role' => 'শুধু অনুমোদিত শিক্ষক প্রোফাইল ও কলেজ থাকা user-কে Principal করা যাবে।']);
                }
                $alreadyAssigned = User::query()->where('id', '!=', $user->id)->where('role', Role::Principal->value)
                    ->where('college_id', $teacher->college_id)->exists();
                if ($alreadyAssigned) {
                    throw ValidationException::withMessages(['role' => 'এই কলেজে ইতোমধ্যে একজন Principal রয়েছে।']);
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
        });

        Flux::toast(variant: 'success', text: 'User role সফলভাবে পরিবর্তন করা হয়েছে।');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('livewire.role-permission-management', [
            'users' => User::query()->with(['teacher.college', 'college'])
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(12),
            'roles' => Role::cases(),
        ]);
    }
}
