<?php

namespace App\Livewire;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;

class TeacherDetails extends Component
{
    #[Locked]
    public Teacher $teacher;

    public function mount(Teacher $teacher): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || ($user->role === Role::Principal && $teacher->college_id === $user->college_id) || ($user->role === Role::Teacher && $teacher->user_id === $user->id && $teacher->approval_status === ApprovalStatus::Approved), 403);
        $this->teacher = $teacher->load([
            'user:id,name,email,mobile_no,picture,digital_signature',
            'college:id,code,name', 'division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name',
            'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute',
        ]);
    }

    public function render(): View
    {
        return view('livewire.teacher-details');
    }
}
