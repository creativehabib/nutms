<?php

namespace App\Livewire;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class IctTrainingSummary extends Component
{
    public function render(): View
    {
        $teachersWithIct = Teacher::select('college_code', 'college_name', 'name', 'ict_training_name', 'other_training_name', 'training_institute')
            ->whereNotNull('ict_training_name')
            ->where('ict_training_name', '!=', '')
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('college_code');

        $teachersWithoutIct = Teacher::select('college_code', 'college_name', 'name')
            ->where(function (Builder $query): void {
                $query->whereNull('ict_training_name')
                    ->orWhere('ict_training_name', '');
            })
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('college_code');

        return view('livewire.ict-training-summary', [
            'teachersWithIct' => $teachersWithIct,
            'teachersWithoutIct' => $teachersWithoutIct,
        ]);
    }
}
