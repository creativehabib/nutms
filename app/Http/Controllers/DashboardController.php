<?php

namespace App\Http\Controllers;

use App\Enums\UserRole as Role;
use App\Models\College;
use App\Models\SystemSetting;
use App\Models\Teacher;
use App\Models\TeacherOtherTraining;
use App\Models\TrainingType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $collegeReport = College::query()
            ->where('is_active', true)
            ->when(auth()->user()->role === Role::Principal, fn ($query) => $query->whereKey(auth()->user()->college_id))
            ->when(auth()->user()->role === Role::Teacher, fn ($query) => $query->whereKey(auth()->user()->college_id))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 1 THEN 1 ELSE 0 END) as with_lab')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 0 OR has_computer_lab IS NULL THEN 1 ELSE 0 END) as without_lab')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 1 THEN COALESCE(desktop_count, 0) + COALESCE(laptop_count, 0) ELSE 0 END) as total_computers')
            ->first();

        $teacherQuery = Teacher::query()
            ->when(auth()->user()->role === Role::Principal, fn ($query) => $query->where('college_id', auth()->user()->college_id))
            ->when(auth()->user()->role === Role::Teacher, fn ($query) => $query->where('user_id', auth()->id()));

        $totalTeachers = (clone $teacherQuery)->count();
        $teachersWithIctTraining = (clone $teacherQuery)->where(function ($query): void {
            $query->whereHas('trainingTypes')
                ->orWhereHas('otherTrainings')
                ->orWhere(fn ($query) => $query->whereNotNull('ict_training_name')->where('ict_training_name', '!=', ''));
        })->count();
        $lastUpdatedAt = (clone $teacherQuery)->max('updated_at');

        $totalColleges = (int) ($collegeReport?->total ?? 0);
        $collegesWithLab = (int) ($collegeReport?->with_lab ?? 0);

        return view('dashboard', [
            'report' => [
                'collegesWithLab' => $collegesWithLab,
                'collegesWithoutLab' => (int) ($collegeReport?->without_lab ?? 0),
                'totalColleges' => $totalColleges,
                'totalComputers' => (int) ($collegeReport?->total_computers ?? 0),
                'labCoverage' => $this->percentage($collegesWithLab, $totalColleges),
                'teachersWithIctTraining' => $teachersWithIctTraining,
                'teachersWithoutIctTraining' => $totalTeachers - $teachersWithIctTraining,
                'totalTeachers' => $totalTeachers,
                'ictTrainingCoverage' => $this->percentage($teachersWithIctTraining, $totalTeachers),
                'lastUpdatedAt' => $lastUpdatedAt
                    ? Carbon::parse($lastUpdatedAt)->format('d M Y, h:i A')
                    : null,
            ],
            'principalStats' => auth()->user()->role === Role::Principal ? $this->principalStats() : null,
        ]);
    }

    private function percentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }

    /** @return array<string, mixed> */
    private function principalStats(): array
    {
        $collegeId = auth()->user()->college_id;
        $retirementAge = SystemSetting::retirementAge();
        $today = now()->startOfDay();
        $teachers = Teacher::query()->where('college_id', $collegeId)->whereNotNull('birth_date')->get(['id', 'user_id', 'name', 'birth_date']);
        $retirementRows = $teachers->map(function (Teacher $teacher) use ($retirementAge): array {
            return [
                'name' => $teacher->display_name,
                'retirement_date' => $teacher->birth_date->copy()->addYears($retirementAge),
            ];
        });

        $catalogTrainings = TrainingType::query()
            ->whereHas('teachers', fn ($query) => $query->where('college_id', $collegeId))
            ->withCount(['teachers' => fn ($query) => $query->where('college_id', $collegeId)])
            ->orderBy('name')->get(['id', 'name'])->map(fn (TrainingType $training): array => ['name' => $training->name, 'count' => $training->teachers_count]);
        $otherTrainings = TeacherOtherTraining::query()->whereHas('teacher', fn ($query) => $query->where('college_id', $collegeId))
            ->selectRaw('name, COUNT(*) as teachers_count')->groupBy('name')->orderBy('name')->get()
            ->map(fn (TeacherOtherTraining $training): array => ['name' => $training->name, 'count' => (int) $training->teachers_count]);

        return [
            'retirementAge' => $retirementAge,
            'retired' => $retirementRows->filter(fn (array $row): bool => $row['retirement_date']->lte($today))->sortBy('retirement_date')->values(),
            'upcomingRetirements' => $retirementRows->filter(fn (array $row): bool => $row['retirement_date']->gt($today) && $row['retirement_date']->lte($today->copy()->addYear()))->sortBy('retirement_date')->values(),
            'missingBirthDates' => Teacher::query()->where('college_id', $collegeId)->whereNull('birth_date')->count(),
            'subjects' => Teacher::query()->where('college_id', $collegeId)->whereNotNull('subject')->where('subject', '!=', '')
                ->selectRaw('subject, COUNT(*) as teachers_count')->groupBy('subject')->orderBy('subject')->get(),
            'trainings' => $catalogTrainings->concat($otherTrainings)->groupBy('name')->map(fn ($rows, string $name): array => ['name' => $name, 'count' => $rows->sum('count')])->values(),
        ];
    }
}
