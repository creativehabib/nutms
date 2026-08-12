<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\Teacher;
use App\Models\TeacherOtherTraining;
use App\Models\Training;
use App\Models\TrainingType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $collegeReport = College::query()
            ->where('is_active', true)
            ->when(auth()->user()->hasRole('principal'), fn ($query) => $query->whereKey(auth()->user()->college_id))
            ->when(auth()->user()->hasRole('teacher'), fn ($query) => $query->whereKey(auth()->user()->college_id))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 1 THEN 1 ELSE 0 END) as with_lab')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 0 OR has_computer_lab IS NULL THEN 1 ELSE 0 END) as without_lab')
            ->selectRaw('SUM(CASE WHEN has_computer_lab = 1 THEN COALESCE(desktop_count, 0) + COALESCE(laptop_count, 0) ELSE 0 END) as total_computers')
            ->first();

        $teacherQuery = Teacher::query()
            ->when(auth()->user()->hasRole('principal'), fn ($query) => $query->where('college_id', auth()->user()->college_id))
            ->when(auth()->user()->hasRole('teacher'), fn ($query) => $query->where('user_id', auth()->id()));

        $totalTeachers = (clone $teacherQuery)->count();
        $teachersWithIctTraining = (clone $teacherQuery)->where(function ($query): void {
            $query->whereHas('trainingTypes')
                ->orWhereHas('otherTrainings');
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
            'principalStats' => auth()->user()->hasRole('principal') ? $this->principalStats() : null,
            'teacherStats' => auth()->user()->hasRole('teacher') ? $this->teacherStats() : null,
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
            'subjects' => Subject::query()
                ->whereHas('teachers', fn ($query) => $query->where('college_id', $collegeId))
                ->withCount(['teachers' => fn ($query) => $query->where('college_id', $collegeId)])
                ->orderBy('name')->get(),
            'trainings' => $catalogTrainings->concat($otherTrainings)->groupBy('name')->map(fn ($rows, string $name): array => ['name' => $name, 'count' => $rows->sum('count')])->values(),
        ];
    }

    /** @return array<string, mixed>|null */
    private function teacherStats(): ?array
    {
        $teacher = Teacher::query()
            ->with(['user', 'subject', 'designation', 'teacherLevel', 'employment', 'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute'])
            ->where('user_id', auth()->id())
            ->first();

        if ($teacher === null) {
            return null;
        }

        $retirementAge = SystemSetting::retirementAge();
        $retirementDate = $teacher->birth_date?->copy()->addYears($retirementAge);
        $today = now()->startOfDay();
        $trainings = $this->teacherTrainings($teacher);
        $completedTrainingCertificates = $this->completedTrainingCertificates($teacher);

        return [
            'profile' => $teacher,
            'retirementAge' => $retirementAge,
            'retirementDate' => $retirementDate,
            'isRetired' => $retirementDate?->lte($today),
            'daysUntilRetirement' => $retirementDate === null ? null : (int) $today->diffInDays($retirementDate, false),
            'lastUpdatedAt' => $teacher->updated_at?->format('d M Y, h:i A'),
            'trainings' => $trainings,
            'completedTrainingCertificates' => $completedTrainingCertificates,
            'completeness' => $this->teacherProfileCompleteness($teacher, $trainings->isNotEmpty()),
        ];
    }

    /** @return array{percentage: int, completed: int, total: int, missing: Collection<int, string>} */
    private function teacherProfileCompleteness(Teacher $teacher, bool $hasTraining): array
    {
        $profileFields = collect([
            'কলেজ' => $teacher->college_id,
            'নাম' => $teacher->display_name,
            'জন্ম তারিখ' => $teacher->birth_date,
            'পদবি' => $teacher->designation?->name,
            'বিষয়' => $teacher->subject?->name,
            'শিক্ষক স্তর' => $teacher->teacherLevel?->name,
            'চাকরির ধরন' => $teacher->employment?->name,
            'বিভাগ' => $teacher->division_id,
            'জেলা' => $teacher->district_id,
            'থানা' => $teacher->thana_id,
            'বর্তমান ঠিকানা' => $teacher->present_address,
            'স্থায়ী ঠিকানা' => $teacher->permanent_address,
            'মোবাইল নম্বর' => $teacher->user?->mobile_no,
            'ইমেইল' => $teacher->user?->email,
            'ব্যাংকের নাম' => $teacher->bank_name,
            'ব্যাংক শাখা' => $teacher->bank_branch_name,
            'ব্যাংক অ্যাকাউন্ট নম্বর' => $teacher->bank_account_number,
            'ব্যাংক রাউটিং নম্বর' => $teacher->bank_routing_number,
            'ট্রেনিং তথ্য' => $hasTraining,
        ]);

        $completed = $profileFields->filter(fn (mixed $value): bool => filled($value))->count();
        $total = $profileFields->count();

        return [
            'percentage' => (int) round(($completed / $total) * 100),
            'completed' => $completed,
            'total' => $total,
            'missing' => $profileFields->filter(fn (mixed $value): bool => blank($value))->keys()->values(),
        ];
    }

    /** @return Collection<int, array{name: string, institute: ?string, year: ?int, certificate_url: ?string}> */
    private function teacherTrainings(Teacher $teacher): Collection
    {
        $catalogTrainings = $teacher->trainingTypes->map(fn (TrainingType $training): array => [
            'name' => $training->name,
            'institute' => $training->trainingInstitute?->name,
            'year' => $training->pivot->training_year ? (int) $training->pivot->training_year : null,
            'certificate_url' => null,
        ]);

        $otherTrainings = $teacher->otherTrainings->map(fn (TeacherOtherTraining $training): array => [
            'name' => $training->name,
            'institute' => $training->trainingInstitute?->name ?: $training->institute_name,
            'year' => $training->training_year,
            'certificate_url' => null,
        ]);

        $scheduledTrainings = Training::query()
            ->whereHas('participants', fn ($query) => $query
                ->whereKey($teacher->user_id)
                ->where('training_user.status', 'Completed'))
            ->orderByDesc('end_date')
            ->get()
            ->map(fn (Training $training): array => [
                'name' => $training->title,
                'institute' => $training->location_or_link,
                'year' => $training->end_date->year,
                'certificate_url' => $training->has_certificate
                    ? route('trainings.certificate', $training)
                    : null,
            ]);

        return $catalogTrainings
            ->concat($otherTrainings)
            ->concat($scheduledTrainings)
            ->unique(fn (array $training): string => mb_strtolower($training['name']).'|'.$training['year'])
            ->sortByDesc('year')
            ->values();
    }

}
