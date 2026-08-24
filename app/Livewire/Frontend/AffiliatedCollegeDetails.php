<?php

namespace App\Livewire\Frontend;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\ProgramLevel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AffiliatedCollegeDetails extends Component
{
    #[Locked]
    public College $college;

    public function mount(College $college, string $slug): void
    {
        abort_unless(
            $college->is_active
                && $college->approval_status === ApprovalStatus::Approved
                && hash_equals($college->publicProfileSlug(), $slug),
            404,
        );

        $this->college = $college
            ->load(['division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name', 'principal:id,name,college_id', 'programs'])
            ->loadCount('teachers');
    }

    public function render(): View
    {
        $programLevelNames = ProgramLevel::query()
            ->whereIn('slug', $this->college->programs->pluck('level'))
            ->pluck('name', 'slug');
        $relatedColleges = $this->college->division_id === null
            ? College::newCollection()
            : College::query()
                ->whereKeyNot($this->college->getKey())
                ->where('division_id', $this->college->division_id)
                ->where('is_active', true)
                ->where('approval_status', ApprovalStatus::Approved)
                ->orderByRaw('CASE WHEN district_id = ? THEN 0 ELSE 1 END', [$this->college->district_id])
                ->orderBy('name')
                ->get(['id', 'name', 'college_name_bn', 'college_code', 'logo', 'district_id', 'division_id']);

        return view('livewire.frontend.affiliated-college-details', [
            'programLevelNames' => $programLevelNames,
            'relatedColleges' => $relatedColleges,
        ])
            ->layout('layouts.frontend', [
                'title' => $this->college->name,
                'description' => str(strip_tags($this->college->about ?: $this->college->name.' affiliated college profile, courses, contact information, and location.'))
                    ->limit(160, '')
                    ->toString(),
                'keywords' => implode(', ', array_filter([$this->college->name, $this->college->college_name_bn, $this->college->college_code, 'National University affiliated college'])),
                'image' => $this->college->logo ? asset('storage/'.$this->college->logo) : null,
            ]);
    }
}
