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
                            $teacher->college?->college_code ?? '-',
                            $teacher->college?->name ?? '-',
                            $teacher->display_name ?: '-',
                            $this->trainingDetails($teacher),
                            $teacher->otherTrainings->pluck('name')->implode(', ') ?: 'উল্লেখ নেই',
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
                            $teacher->college?->college_code ?? '-',
                            $teacher->college?->name ?? '-',
                            $teacher->display_name ?: '-',
                            $teacher->subject?->name ?: 'উল্লেখ নেই',
                            $teacher->designation?->name ?: 'উল্লেখ নেই',
                            $teacher->teacherLevel?->name ?: 'উল্লেখ নেই',
                            $teacher->employment?->name ?: 'উল্লেখ নেই',
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
            'teachersByCollege' => $teachers->getCollection()->groupBy('college_id'),
        ])->layout('layouts.app', ['title' => 'ICT Training Summary']);
    }

    private function teachersWithIctQuery(): Builder
    {
        return Teacher::query()
            ->with(['user:id,name', 'college:id,college_code,name', 'trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute'])
            ->where(fn (Builder $query): Builder => $query->whereHas('trainingTypes')->orWhereHas('otherTrainings'))
            ->orderBy('college_id')
            ->orderBy('name')
            ->orderBy('id');
    }

    private function teachersWithoutIctQuery(): Builder
    {
        return Teacher::query()
            ->with(['user:id,name', 'college:id,college_code,name', 'subject:id,name', 'designation:id,name', 'teacherLevel:id,name', 'employment:id,name'])
            ->doesntHave('trainingTypes')
            ->doesntHave('otherTrainings')
            ->orderBy('college_id')
            ->orderBy('name')
            ->orderBy('id');
    }

    public function trainingDetails(Teacher $teacher): string
    {
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
            ->groupBy('college_id');
    }

    /**
     * @return Collection<string, Collection<int, Teacher>>
     */
    private function teachersWithoutIct(): Collection
    {
        return $this->teachersWithoutIctQuery()
            ->get()
            ->groupBy('college_id');
    }
}
