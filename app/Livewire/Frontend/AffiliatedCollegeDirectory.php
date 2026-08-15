<?php

namespace App\Livewire\Frontend;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\District;
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

    #[Url(history: true)]
    public string $district = '';

    #[Url(history: true)]
    public string $collegeType = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDivision(): void
    {
        $this->reset('district');
        $this->resetPage();
    }

    public function updatedDistrict(): void
    {
        $this->resetPage();
    }

    public function updatedCollegeType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'collegeType', 'division', 'district']);
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
            ->when($this->collegeType !== '', fn (Builder $query) => $query->where('college_type', $this->collegeType))
            ->when($this->division !== '', fn (Builder $query) => $query->where('division_id', $this->division))
            ->when($this->district !== '', fn (Builder $query) => $query->where('district_id', $this->district))
            ->orderBy('name')
            ->paginate(12);

        $divisions = Division::query()
            ->whereHas('colleges', fn (Builder $query) => $this->applyPublicVisibility($query))
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name']);

        $districts = $this->division === ''
            ? collect()
            : District::query()
                ->where('division_id', $this->division)
                ->whereHas('colleges', fn (Builder $query) => $this->applyPublicVisibility($query))
                ->orderBy('name')
                ->get(['id', 'division_id', 'name', 'bn_name']);

        return view('livewire.frontend.affiliated-college-directory', compact('colleges', 'districts', 'divisions'))
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
