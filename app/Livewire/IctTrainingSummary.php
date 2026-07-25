<?php

namespace App\Livewire;

use App\Exports\SummaryExport;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IctTrainingSummary extends Component
{
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
                        'ট্রেনিং নেই',
                    ],
                )->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'শিক্ষকের নাম', 'অবস্থা'],
                'teachers-without-ict-training.xlsx',
            ],
            default => abort(404),
        };

        return Excel::download(new SummaryExport($rows, $headings), $filename);
    }

    public function render(): View
    {
        return view('livewire.ict-training-summary', [
            'teachersWithIct' => $this->teachersWithIct(),
            'teachersWithoutIct' => $this->teachersWithoutIct(),
        ]);
    }

    /**
     * @return Collection<string, Collection<int, Teacher>>
     */
    private function teachersWithIct(): Collection
    {
        return Teacher::select('college_code', 'college_name', 'name', 'ict_training_name', 'other_training_name', 'training_institute')
            ->whereNotNull('ict_training_name')
            ->where('ict_training_name', '!=', '')
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('college_code');
    }

    /**
     * @return Collection<string, Collection<int, Teacher>>
     */
    private function teachersWithoutIct(): Collection
    {
        return Teacher::select('college_code', 'college_name', 'name')
            ->where(function (Builder $query): void {
                $query->whereNull('ict_training_name')
                    ->orWhere('ict_training_name', '');
            })
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('college_code');
    }
}
