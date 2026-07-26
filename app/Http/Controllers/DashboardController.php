<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $colleges = Teacher::query()
            ->selectRaw("college_code, MAX(CASE WHEN LOWER(has_computer_lab) = 'yes' THEN 1 ELSE 0 END) as has_lab")
            ->whereNotNull('college_code')
            ->groupBy('college_code');

        $collegeReport = DB::query()
            ->fromSub($colleges, 'colleges')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_lab = 1 THEN 1 ELSE 0 END) as with_lab')
            ->selectRaw('SUM(CASE WHEN has_lab = 0 THEN 1 ELSE 0 END) as without_lab')
            ->first();

        $teacherReport = Teacher::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NOT NULL AND ict_training_name != '' THEN 1 ELSE 0 END) as with_ict_training")
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NULL OR ict_training_name = '' THEN 1 ELSE 0 END) as without_ict_training")
            ->first();

        return view('dashboard', [
            'report' => [
                'collegesWithLab' => (int) ($collegeReport?->with_lab ?? 0),
                'collegesWithoutLab' => (int) ($collegeReport?->without_lab ?? 0),
                'totalColleges' => (int) ($collegeReport?->total ?? 0),
                'teachersWithIctTraining' => (int) ($teacherReport?->with_ict_training ?? 0),
                'teachersWithoutIctTraining' => (int) ($teacherReport?->without_ict_training ?? 0),
                'totalTeachers' => (int) ($teacherReport?->total ?? 0),
            ],
        ]);
    }
}
