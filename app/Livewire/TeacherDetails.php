<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherDetails extends Component
{
    #[Locked]
    public Teacher $teacher;

    public function mount(Teacher $teacher): void
    {
        $user = auth()->user();
        abort_unless($user->can('teachers.view'), 403);
        abort_unless($user->isAdmin() || ($user->hasRole('principal') && $teacher->college_id === $user->college_id) || ($user->hasRole('teacher') && $teacher->user_id === $user->id && $teacher->approval_status === ApprovalStatus::Approved), 403);
        $this->teacher = $teacher->load([
            'user:id,name,email,mobile_no,picture,digital_signature',
            'college:id,college_code,name', 'division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name',
            'subject:id,name', 'designation:id,name', 'teacherLevel:id,name', 'employment:id,name',
            'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute',
        ]);
    }

    public function render(): View
    {
        return view('livewire.teacher-details');
    }
}
