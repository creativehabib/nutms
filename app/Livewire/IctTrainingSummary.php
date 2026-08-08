<?php

namespace App\Livewire;

use App\Exports\SummaryExport;
use App\Models\Teacher;
use App\Models\TeacherOtherTraining;
use App\Models\TrainingType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IctTrainingSummary extends Component
{
    use WithPagination;

    public string $activeTab = 'with_ict';

    public function showTab(string $tab): void
    {
        abort_unless(in_array($tab, ['with_ict', 'without_ict'], true), 404);

        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function export(string $tab): BinaryFileResponse
    {
        [$rows, $headings, $filename] = match ($tab) {
            'with_ict' => [
                $this->teachersWithIct()->flatMap(
                    fn (Collection $teachers): Collection => $teachers->values()->map(
                        fn (Teacher $teacher, int $index): array => [
                            $index + 1,
                            $teacher->college_code ?? '-',
                            $teacher->college_name ?? '-',
                            $teacher->display_name ?: '-',
                            $this->trainingDetails($teacher),
                            $teacher->other_training_name ?: 'উল্লেখ নেই',
                            $this->trainingInstitutes($teacher),
                        ],
                    ),
                )->values()->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'শিক্ষকের নাম', 'আইসিটি ট্রেনিংয়ের নাম', 'অন্যান্য ট্রেনিংয়ের নাম', 'ট্রেনিং ইনস্টিটিউট'],
                'teachers-with-ict-training.xlsx',
            ],
            'without_ict' => [
                $this->teachersWithoutIct()->flatMap(
                    fn (Collection $teachers): Collection => $teachers->values()->map(
                        fn (Teacher $teacher, int $index): array => [
                            $index + 1,
                            $teacher->college_code ?? '-',
                            $teacher->college_name ?? '-',
                            $teacher->display_name ?: '-',
                            $teacher->subject ?: 'উল্লেখ নেই',
                            $teacher->designation ?: 'উল্লেখ নেই',
                            $teacher->teacher_level ?: 'উল্লেখ নেই',
                            $teacher->employment_type ?: 'উল্লেখ নেই',
                            'ট্রেনিং নেই',
                        ],
                    ),
                )->values()->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'শিক্ষকের নাম', 'বিষয়', 'পদবি', 'শিক্ষক স্তর', 'চাকরির ধরন', 'অবস্থা'],
                'teachers-without-ict-training.xlsx',
            ],
            default => abort(404),
        };

        return Excel::download(new SummaryExport($rows, $headings), $filename);
    }

    public function render(): View
    {
        $teachers = $this->activeTab === 'with_ict'
            ? $this->teachersWithIctQuery()->paginate(50)
            : $this->teachersWithoutIctQuery()->paginate(50);

        return view('livewire.ict-training-summary', [
            'teachers' => $teachers,
            'teachersByCollege' => $teachers->getCollection()->groupBy('college_code'),
        ])->layout('layouts.app', ['title' => 'ICT Training Summary']);
    }

    private function teachersWithIctQuery(): Builder
    {
        return Teacher::select('id', 'user_id', 'college_code', 'college_name', 'name', 'ict_training_name', 'other_training_name', 'training_institute')
            ->with(['user:id,name', 'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute'])
            ->where(function (Builder $query): void {
                $query->whereHas('trainingTypes')
                    ->orWhereHas('otherTrainings')
                    ->orWhere(function (Builder $query): void {
                        $query->whereNotNull('ict_training_name')->where('ict_training_name', '!=', '');
                    });
            })
            ->orderBy('college_code')
            ->orderBy('name')
            ->orderBy('id');
    }

    private function teachersWithoutIctQuery(): Builder
    {
        return Teacher::select('id', 'user_id', 'college_code', 'college_name', 'name', 'subject', 'designation', 'teacher_level', 'employment_type')
            ->with('user:id,name')
            ->doesntHave('trainingTypes')
            ->doesntHave('otherTrainings')
            ->where(function (Builder $query): void {
                $query->whereNull('ict_training_name')
                    ->orWhere('ict_training_name', '');
            })
            ->orderBy('college_code')
            ->orderBy('name')
            ->orderBy('id');
    }

    public function trainingDetails(Teacher $teacher): string
    {
        if ($teacher->trainingTypes->isEmpty() && $teacher->otherTrainings->isEmpty()) {
            return $teacher->ict_training_name ?: 'উল্লেখ নেই';
        }

        $units = ['hours' => 'ঘণ্টা', 'days' => 'দিন', 'weeks' => 'সপ্তাহ', 'months' => 'মাস'];

        $catalogTrainings = $teacher->trainingTypes->map(function (TrainingType $trainingType) use ($units): string {
            $duration = $trainingType->duration_value
                ? "{$trainingType->duration_value} ".($units[$trainingType->duration_unit] ?? '')
                : 'সময়কাল অনির্ধারিত';

            return "{$trainingType->name} ({$trainingType->pivot->training_year}, {$duration})";
        });
        $otherTrainings = $teacher->otherTrainings->map(function (TeacherOtherTraining $training) use ($units): string {
            $duration = $training->duration_value
                ? "{$training->duration_value} ".($units[$training->duration_unit] ?? '')
                : 'সময়কাল অনির্ধারিত';

            return "{$training->name} (অন্যান্য, {$training->training_year}, {$duration})";
        });

        return $catalogTrainings->concat($otherTrainings)->implode(', ');
    }

    public function trainingInstitutes(Teacher $teacher): string
    {
        if ($teacher->trainingTypes->isEmpty() && $teacher->otherTrainings->isEmpty()) {
            return $teacher->training_institute ?: 'উল্লেখ নেই';
        }

        return $teacher->trainingTypes->pluck('trainingInstitute.name')
            ->concat($teacher->otherTrainings->map(fn (TeacherOtherTraining $training): ?string => $training->trainingInstitute?->name ?? $training->institute_name))
            ->filter()->unique()->implode(', ');
    }

    /**
     * @return Collection<string, Collection<int, Teacher>>
     */
    private function teachersWithIct(): Collection
    {
        return $this->teachersWithIctQuery()
            ->get()
            ->groupBy('college_code');
    }

    /**
     * @return Collection<string, Collection<int, Teacher>>
     */
    private function teachersWithoutIct(): Collection
    {
        return $this->teachersWithoutIctQuery()
            ->get()
            ->groupBy('college_code');
    }
}
