<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Models\College;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class CollegeManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $collegeTypeFilter = '';

    public string $approvalStatusFilter = '';

    public bool $showTrashed = false;

    /** @var array<int, string> */
    public array $selectedCollegeIds = [];

    public bool $selectAllOnPage = false;

    /** @var array<int, int> */
    #[Locked]
    public array $deletingCollegeIds = [];

    #[Locked]
    public string $deletingCollegeName = '';

    #[Locked]
    public bool $permanentDeletion = false;

    public function updatedSearch(): void
    {
        $this->resetPaginationAndSelection();
    }

    public function updatedCollegeTypeFilter(): void
    {
        $this->resetPaginationAndSelection();
    }

    public function updatedApprovalStatusFilter(): void
    {
        $this->resetPaginationAndSelection();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'collegeTypeFilter', 'approvalStatusFilter');
        $this->resetPaginationAndSelection();
    }

    public function toggleTrashed(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->showTrashed = ! $this->showTrashed;
        $this->resetPaginationAndSelection();
    }

    public function toggleSelectAllOnPage(): void
    {
        $this->selectAllOnPage = ! $this->selectAllOnPage;

        $this->selectedCollegeIds = $this->selectAllOnPage
            ? $this->filteredCollegesQuery()->orderBy('name')->forPage($this->getPage(), 10)->pluck('id')->map(fn (int $id): string => (string) $id)->all()
            : [];
    }

    public function updatedSelectedCollegeIds(): void
    {
        $this->selectAllOnPage = false;
    }

    public function confirmDeletion(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $college = College::query()->findOrFail($collegeId);

        $this->prepareDeletion([$college->id], $college->name, false);
    }

    public function confirmBulkDeletion(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $colleges = College::query()->whereKey($this->normalizedSelectedCollegeIds())->get(['id', 'name']);

        if ($colleges->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'মুছে ফেলার জন্য অন্তত একটি কলেজ নির্বাচন করুন।');

            return;
        }

        $this->prepareDeletion(
            $colleges->pluck('id')->all(),
            $colleges->count() === 1 ? $colleges->first()->name : "নির্বাচিত {$colleges->count()}টি কলেজ",
            false,
        );
    }

    public function confirmPermanentDeletion(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $college = College::onlyTrashed()->findOrFail($collegeId);

        $this->prepareDeletion([$college->id], $college->name, true);
    }

    public function confirmBulkPermanentDeletion(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $colleges = College::onlyTrashed()->whereKey($this->normalizedSelectedCollegeIds())->get(['id', 'name']);

        if ($colleges->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'স্থায়ীভাবে মুছে ফেলার জন্য অন্তত একটি কলেজ নির্বাচন করুন।');

            return;
        }

        $this->prepareDeletion(
            $colleges->pluck('id')->all(),
            $colleges->count() === 1 ? $colleges->first()->name : "নির্বাচিত {$colleges->count()}টি কলেজ",
            true,
        );
    }

    public function deleteConfirmed(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($this->deletingCollegeIds === []) {
            return;
        }

        $deletedCount = $this->permanentDeletion
            ? College::onlyTrashed()->whereKey($this->deletingCollegeIds)->forceDelete()
            : College::query()->whereKey($this->deletingCollegeIds)->delete();

        $message = $this->permanentDeletion
            ? "{$deletedCount}টি কলেজ স্থায়ীভাবে মুছে ফেলা হয়েছে।"
            : "{$deletedCount}টি কলেজ ট্র্যাশে পাঠানো হয়েছে।";

        $this->resetDeletionState();
        $this->resetSelection();
        Flux::modal('confirm-college-deletion')->close();
        Flux::toast(variant: 'success', text: $message);
    }

    public function cancelDeletion(): void
    {
        $this->resetDeletionState();
    }

    public function restore(int $collegeId): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        College::onlyTrashed()->whereKey($collegeId)->restore();
        $this->resetSelection();
        Flux::toast(variant: 'success', text: 'কলেজটি পুনরুদ্ধার করা হয়েছে।');
    }

    public function restoreSelected(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $restoredCount = College::onlyTrashed()->whereKey($this->normalizedSelectedCollegeIds())->restore();

        if ($restoredCount === 0) {
            Flux::toast(variant: 'warning', text: 'পুনরুদ্ধারের জন্য অন্তত একটি কলেজ নির্বাচন করুন।');

            return;
        }

        $this->resetSelection();
        Flux::toast(variant: 'success', text: "{$restoredCount}টি কলেজ পুনরুদ্ধার করা হয়েছে।");
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
            'colleges' => $this->filteredCollegesQuery()
                ->with(['division:id,name', 'district:id,name', 'thana:id,name', 'principal:id,name,college_id,approval_status'])
                ->withCount('teachers')
                ->orderBy('name')
                ->paginate(10),
        ])->layout('layouts.app', ['title' => 'College Management']);
    }

    private function filteredCollegesQuery(): Builder
    {
        $query = $this->showTrashed ? College::onlyTrashed() : College::query();
        $searchTerm = trim($this->search);

        $query->when(auth()->user()->hasRole('principal'), fn (Builder $query): Builder => $query->whereKey(auth()->user()->college_id));

        if ($searchTerm !== '') {
            $searchPattern = "%{$searchTerm}%";
            $query->where(function (Builder $query) use ($searchPattern): void {
                $query->where('name', 'like', $searchPattern)
                    ->orWhere('code', 'like', $searchPattern)
                    ->orWhere('principal_name', 'like', $searchPattern)
                    ->orWhere('address', 'like', $searchPattern)
                    ->orWhereHas('division', fn (Builder $relation): Builder => $relation->where('name', 'like', $searchPattern))
                    ->orWhereHas('district', fn (Builder $relation): Builder => $relation->where('name', 'like', $searchPattern))
                    ->orWhereHas('thana', fn (Builder $relation): Builder => $relation->where('name', 'like', $searchPattern));
            });
        }

        $query->when($this->collegeTypeFilter !== '', fn (Builder $query): Builder => $query->where('college_type', $this->collegeTypeFilter));
        $query->when($this->approvalStatusFilter !== '', fn (Builder $query): Builder => $query->where('approval_status', $this->approvalStatusFilter));

        return $query;
    }

    /** @param array<int, int> $collegeIds */
    private function prepareDeletion(array $collegeIds, string $collegeName, bool $permanent): void
    {
        $this->deletingCollegeIds = $collegeIds;
        $this->deletingCollegeName = $collegeName;
        $this->permanentDeletion = $permanent;
        Flux::modal('confirm-college-deletion')->show();
    }

    /** @return array<int, int> */
    private function normalizedSelectedCollegeIds(): array
    {
        return collect($this->selectedCollegeIds)->map(fn (string $id): int => (int) $id)->unique()->values()->all();
    }

    private function resetPaginationAndSelection(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    private function resetSelection(): void
    {
        $this->reset('selectedCollegeIds', 'selectAllOnPage');
    }

    private function resetDeletionState(): void
    {
        $this->reset('deletingCollegeIds', 'deletingCollegeName', 'permanentDeletion');
    }
}
