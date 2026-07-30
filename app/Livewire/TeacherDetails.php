<?php

namespace App\Livewire;

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
        $this->teacher = $teacher->load([
            'college:id,code,name', 'division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name',
            'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute',
        ]);
    }

    public function render(): View
    {
        return view('livewire.teacher-details');
    }
}
