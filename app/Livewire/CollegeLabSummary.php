<?php
namespace App\Livewire;

use App\Models\Teacher;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CollegeLabSummary extends Component
{
    public function render()
    {
        // ডেটাবেস থেকে সব কলেজের ডেটা আনা হচ্ছে
        $colleges = Teacher::select(
            'college_code',
            'college_name',
            // যদি অন্তত একজন শিক্ষকের ডেটাতে 'yes' থাকে, তবে সেটিকে ল্যাব আছে বলে ধরা হবে
            DB::raw("MAX(CASE WHEN LOWER(has_computer_lab) = 'yes' THEN 1 ELSE 0 END) as has_lab"),
            DB::raw("MAX(computer_count) as total_computers")
        )
            ->whereNotNull('college_code')
            ->groupBy('college_code', 'college_name')
            ->orderBy('college_code', 'asc')
            ->get();

        // দুটি আলাদা কালেকশনে ভাগ করা
        $collegesWithLab = $colleges->where('has_lab', 1);
        $collegesWithoutLab = $colleges->where('has_lab', 0);

        return view('livewire.college-lab-summary', [
            'collegesWithLab' => $collegesWithLab,
            'collegesWithoutLab' => $collegesWithoutLab,
        ]);
    }
}
