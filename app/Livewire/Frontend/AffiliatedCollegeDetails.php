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

    public function mount(College $college): void
    {
        abort_unless(
            $college->is_active && $college->approval_status === ApprovalStatus::Approved,
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

        return view('livewire.frontend.affiliated-college-details', [
            'programLevelNames' => $programLevelNames,
        ])
            ->layout('layouts.frontend', ['title' => $this->college->name]);
    }
}
