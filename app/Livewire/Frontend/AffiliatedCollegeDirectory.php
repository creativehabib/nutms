<?php

namespace App\Livewire\Frontend;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Division;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AffiliatedCollegeDirectory extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $division = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDivision(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'division']);
        $this->resetPage();
    }

    public function render(): View
    {
        $colleges = $this->publicColleges()
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('college_code', 'like', "%{$this->search}%")
                        ->orWhereHas('programs', function (Builder $query): void {
                            $query->where('name', 'like', "%{$this->search}%")
                                ->orWhere('items', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->division !== '', fn (Builder $query) => $query->where('division_id', $this->division))
            ->orderBy('name')
            ->paginate(12);

        $divisions = Division::query()
            ->whereHas('colleges', fn (Builder $query) => $this->applyPublicVisibility($query))
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name']);

        return view('livewire.frontend.affiliated-college-directory', compact('colleges', 'divisions'))
            ->layout('layouts.frontend', ['title' => 'অধিভুক্ত কলেজসমূহ']);
    }

    /** @return Builder<College> */
    private function publicColleges(): Builder
    {
        return $this->applyPublicVisibility(College::query());
    }

    /**
     * @param  Builder<College>  $query
     * @return Builder<College>
     */
    private function applyPublicVisibility(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved);
    }
}
