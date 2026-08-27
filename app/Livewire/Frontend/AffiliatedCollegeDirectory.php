<?php

namespace App\Livewire\Frontend;

use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\ProgramLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class AffiliatedCollegeDirectory extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';

    public string $division = '';

    public string $district = '';

    public string $collegeType = '';

    public bool $showCollegeModal = false;

    public ?int $selectedCollegeId = null;

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

    public function viewCollege(int $collegeId): void
    {
        $college = $this->publicColleges()->findOrFail($collegeId);

        $this->selectedCollegeId = $college->id;
        $this->showCollegeModal = true;
    }

    public function closeCollegeModal(): void
    {
        $this->reset('showCollegeModal', 'selectedCollegeId');
    }

    public function render(): View
    {
        $colleges = $this->publicColleges()
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
            ->whereHas('colleges', fn (Builder $query) => $query->publiclyVisible())
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name']);

        $districts = $this->division === ''
            ? collect()
            : District::query()
                ->where('division_id', $this->division)
                ->whereHas('colleges', fn (Builder $query) => $query->publiclyVisible())
                ->orderBy('name')
                ->get(['id', 'division_id', 'name', 'bn_name']);

        $selectedCollege = $this->selectedCollege();
        $programLevelNames = $selectedCollege === null
            ? collect()
            : ProgramLevel::query()
                ->whereIn('slug', $selectedCollege->programs->pluck('level'))
                ->pluck('name', 'slug');

        return view('livewire.frontend.affiliated-college-directory', compact(
            'colleges',
            'districts',
            'divisions',
            'programLevelNames',
            'selectedCollege',
        ))
            ->layout('layouts.frontend', ['title' => 'অধিভুক্ত কলেজসমূহ']);
    }

    private function selectedCollege(): ?College
    {
        if ($this->selectedCollegeId === null) {
            return null;
        }

        return $this->publicColleges()
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name', 'principal:id,name,college_id', 'programs'])
            ->withCount('teachers')
            ->find($this->selectedCollegeId);
    }

    /** @return Builder<College> */
    private function publicColleges(): Builder
    {
        return College::query()->publiclyVisible();
    }
}
