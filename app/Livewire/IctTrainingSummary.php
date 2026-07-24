<?php

namespace App\Livewire;

use App\Models\Teacher;
use Livewire\Component;

class IctTrainingSummary extends Component
{
    public function render()
    {
        // যেসব শিক্ষকের ICT ট্রেনিং আছে (কলেজ অনুযায়ী গ্রুপ করা)
        $teachersWithIct = Teacher::select('college_code', 'college_name', 'name', 'ict_training_name', 'training_institute')
            ->whereNotNull('ict_training_name')
            ->where('ict_training_name', '!=', '')
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc') // শিক্ষকের নাম অনুযায়ী সাজানো
            ->get()
            ->groupBy('college_code'); // কলেজ কোড দিয়ে গ্রুপ করা

        // যেসব শিক্ষকের ICT ট্রেনিং নেই (কলেজ অনুযায়ী গ্রুপ করা)
        $teachersWithoutIct = Teacher::select('college_code', 'college_name', 'name')
            ->where(function($query) {
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
