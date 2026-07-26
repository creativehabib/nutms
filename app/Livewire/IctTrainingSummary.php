<?php

namespace App\Livewire;

use App\Exports\SummaryExport;
use App\Models\Teacher;
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
                $this->teachersWithIct()->flatten(1)->values()->map(
                    fn (Teacher $teacher, int $index): array => [
                        $index + 1,
                        $teacher->college_code ?? '-',
                        $teacher->college_name ?? '-',
                        $teacher->name ?? '-',
                        $teacher->ict_training_name ?? '-',
                        $teacher->other_training_name ?: 'উল্লেখ নেই',
                        $teacher->training_institute ?: 'উল্লেখ নেই',
                    ],
                )->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'শিক্ষকের নাম', 'আইসিটি ট্রেনিংয়ের নাম', 'অন্যান্য ট্রেনিংয়ের নাম', 'ট্রেনিং ইনস্টিটিউট'],
                'teachers-with-ict-training.xlsx',
            ],
            'without_ict' => [
                $this->teachersWithoutIct()->flatten(1)->values()->map(
                    fn (Teacher $teacher, int $index): array => [
                        $index + 1,
                        $teacher->college_code ?? '-',
                        $teacher->college_name ?? '-',
                        $teacher->name ?? '-',
                        $teacher->subject ?: 'উল্লেখ নেই',
                        $teacher->designation ?: 'উল্লেখ নেই',
                        $teacher->teacher_level ?: 'উল্লেখ নেই',
                        $teacher->employment_type ?: 'উল্লেখ নেই',
                        'ট্রেনিং নেই',
                    ],
                )->all(),
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
        ]);
    }

    private function teachersWithIctQuery(): Builder
    {
        return Teacher::select('id', 'college_code', 'college_name', 'name', 'ict_training_name', 'other_training_name', 'training_institute')
            ->whereNotNull('ict_training_name')
            ->where('ict_training_name', '!=', '')
            ->orderBy('college_code')
            ->orderBy('name')
            ->orderBy('id');
    }

    private function teachersWithoutIctQuery(): Builder
    {
        return Teacher::select('id', 'college_code', 'college_name', 'name', 'subject', 'designation', 'teacher_level', 'employment_type')
            ->where(function (Builder $query): void {
                $query->whereNull('ict_training_name')
                    ->orWhere('ict_training_name', '');
            })
            ->orderBy('college_code')
            ->orderBy('name')
            ->orderBy('id');
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
