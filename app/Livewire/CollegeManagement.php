<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\College;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CollegeManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $college = College::query()->withCount('teachers')->findOrFail($id);
        if ($college->teachers_count > 0) {
            Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় কলেজটি মুছতে পারবেন না। নিষ্ক্রিয় করুন।');

            return;
        }

        $college->delete();
        Flux::toast(variant: 'success', text: 'কলেজটি মুছে ফেলা হয়েছে।');
    }

    public function approvePrincipal(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        DB::transaction(function () use ($collegeId): void {
            $principal = User::query()
                ->where('role', UserRole::Principal->value)
                ->where('college_id', $collegeId)
                ->where('approval_status', ApprovalStatus::Pending)
                ->lockForUpdate()
                ->firstOrFail();
            $principal->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now()]);
            College::query()->whereKey($collegeId)->update(['submitted_by' => $principal->id]);
        });

        Flux::toast(variant: 'success', text: 'Principal account অনুমোদিত হয়েছে।');
    }

    public function rejectPrincipal(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        User::query()->where('role', UserRole::Principal->value)->where('college_id', $collegeId)
            ->where('approval_status', ApprovalStatus::Pending)->firstOrFail()
            ->update(['approval_status' => ApprovalStatus::Rejected, 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function approveCollege(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $college = College::query()->where('approval_status', ApprovalStatus::Pending)->findOrFail($collegeId);
        $college->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now(), 'is_active' => true]);
        $college->submitter?->update(['college_id' => $college->id]);
    }

    public function render(): View
    {
        return view('livewire.college-management', [
            'colleges' => College::query()->when(auth()->user()->role === UserRole::Principal, fn ($query) => $query->whereKey(auth()->user()->college_id))->with(['district:id,name', 'thana:id,name', 'principal:id,name,college_id,approval_status'])->withCount('teachers')
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(10),
        ]);
    }
}
