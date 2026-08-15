<?php

namespace App\Livewire;

use App\Models\College;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Enums\ApprovalStatus;

class CollegeDetails extends Component
{
    #[Locked]
    public College $college;

    public function mount(College $college): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || ($user->hasRole('principal') && $college->id === $user->college_id && $user->isApproved() && $college->approval_status === ApprovalStatus::Approved), 403);
        $this->college = $college->load(['division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name', 'principal:id,name,college_id', 'programs'])
            ->loadCount('teachers');
    }

    public function render(): View
    {
        return view('livewire.college-details');
    }
}
