<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\College;
use App\Models\Teacher;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ApprovalManagement extends Component
{
    public function approvePrincipal(int $userId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        DB::transaction(function () use ($userId): void {
            $principal = User::query()
                ->where('role', UserRole::Principal->value)
                ->where('approval_status', ApprovalStatus::Pending)
                ->findOrFail($userId);
            abort_if($principal->college_id === null, 422, 'Principal-এর কলেজ নির্বাচন করা নেই।');

            $principal->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now()]);
            College::query()->whereKey($principal->college_id)->update(['submitted_by' => $principal->id]);
        });
        Flux::toast(variant: 'success', text: 'Principal account অনুমোদিত হয়েছে।');
    }

    public function rejectPrincipal(int $userId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        User::query()
            ->where('role', UserRole::Principal->value)
            ->where('approval_status', ApprovalStatus::Pending)
            ->findOrFail($userId)
            ->update(['approval_status' => ApprovalStatus::Rejected, 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function approveCollege(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        DB::transaction(function () use ($collegeId): void {
            $college = College::query()->where('approval_status', ApprovalStatus::Pending)->findOrFail($collegeId);
            $college->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now(), 'is_active' => true]);
            $college->submitter?->update(['college_id' => $college->id]);
        });
        Flux::toast(variant: 'success', text: 'কলেজ প্রোফাইল অনুমোদিত হয়েছে।');
    }

    public function approveTeacher(int $teacherId): void
    {
        $user = auth()->user();
        $teacher = Teacher::query()->where('approval_status', ApprovalStatus::Pending)->findOrFail($teacherId);
        abort_unless($user->isAdmin() || ($user->role === UserRole::Principal && $teacher->college_id === $user->college_id), 403);

        DB::transaction(function () use ($teacher, $user): void {
            $teacher->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => $user->id, 'approved_at' => now()]);
            $teacher->user?->update(['college_id' => $teacher->college_id]);
        });
        Flux::toast(variant: 'success', text: 'শিক্ষক প্রোফাইল অনুমোদিত হয়েছে।');
    }

    public function rejectCollege(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        College::query()->where('approval_status', ApprovalStatus::Pending)->findOrFail($collegeId)
            ->update(['approval_status' => ApprovalStatus::Rejected, 'approved_by' => auth()->id(), 'approved_at' => now(), 'is_active' => false]);
    }

    public function rejectTeacher(int $teacherId): void
    {
        $user = auth()->user();
        $teacher = Teacher::query()->where('approval_status', ApprovalStatus::Pending)->findOrFail($teacherId);
        abort_unless($user->isAdmin() || ($user->role === UserRole::Principal && $teacher->college_id === $user->college_id), 403);
        $teacher->update(['approval_status' => ApprovalStatus::Rejected, 'approved_by' => $user->id, 'approved_at' => now()]);
    }

    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.approval-management', [
            'principals' => $user->isAdmin() ? User::query()->where('role', UserRole::Principal->value)->where('approval_status', ApprovalStatus::Pending)->with('college')->latest()->get() : collect(),
            'colleges' => $user->isAdmin() ? College::query()->where('approval_status', ApprovalStatus::Pending)->with('submitter')->latest()->get() : collect(),
            'teachers' => Teacher::query()->where('approval_status', ApprovalStatus::Pending)
                ->when($user->role === UserRole::Principal, fn ($query) => $query->where('college_id', $user->college_id))
                ->with(['college', 'user'])->latest()->get(),
        ]);
    }
}
