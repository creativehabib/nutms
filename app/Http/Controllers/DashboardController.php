<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\UserRole;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $colleges = Teacher::query()
            ->selectRaw("college_code, MAX(CASE WHEN LOWER(has_computer_lab) = 'yes' THEN 1 ELSE 0 END) as has_lab")
            ->selectRaw('MAX(COALESCE(computer_count, 0)) as computer_count')
            ->whereNotNull('college_code')
            ->when(auth()->user()->role === UserRole::Principal, fn ($query) => $query->where('college_id', auth()->user()->college_id))
            ->when(auth()->user()->role === UserRole::Teacher, fn ($query) => $query->where('user_id', auth()->id()))
            ->groupBy('college_code');

        $collegeReport = DB::query()
            ->fromSub($colleges, 'colleges')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_lab = 1 THEN 1 ELSE 0 END) as with_lab')
            ->selectRaw('SUM(CASE WHEN has_lab = 0 THEN 1 ELSE 0 END) as without_lab')
            ->selectRaw('SUM(CASE WHEN has_lab = 1 THEN computer_count ELSE 0 END) as total_computers')
            ->first();

        $teacherReport = Teacher::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NOT NULL AND ict_training_name != '' THEN 1 ELSE 0 END) as with_ict_training")
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NULL OR ict_training_name = '' THEN 1 ELSE 0 END) as without_ict_training")
            ->selectRaw('MAX(updated_at) as last_updated_at')
            ->when(auth()->user()->role === UserRole::Principal, fn ($query) => $query->where('college_id', auth()->user()->college_id))
            ->when(auth()->user()->role === UserRole::Teacher, fn ($query) => $query->where('user_id', auth()->id()))
            ->first();

        $totalColleges = (int) ($collegeReport?->total ?? 0);
        $collegesWithLab = (int) ($collegeReport?->with_lab ?? 0);
        $totalTeachers = (int) ($teacherReport?->total ?? 0);
        $teachersWithIctTraining = (int) ($teacherReport?->with_ict_training ?? 0);

        return view('dashboard', [
            'report' => [
                'collegesWithLab' => $collegesWithLab,
                'collegesWithoutLab' => (int) ($collegeReport?->without_lab ?? 0),
                'totalColleges' => $totalColleges,
                'totalComputers' => (int) ($collegeReport?->total_computers ?? 0),
                'labCoverage' => $this->percentage($collegesWithLab, $totalColleges),
                'teachersWithIctTraining' => $teachersWithIctTraining,
                'teachersWithoutIctTraining' => (int) ($teacherReport?->without_ict_training ?? 0),
                'totalTeachers' => $totalTeachers,
                'ictTrainingCoverage' => $this->percentage($teachersWithIctTraining, $totalTeachers),
                'lastUpdatedAt' => $teacherReport?->last_updated_at
                    ? Carbon::parse($teacherReport->last_updated_at)->format('d M Y, h:i A')
                    : null,
            ],
        ]);
    }

    private function percentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
}
