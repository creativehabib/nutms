<?php

namespace App\Livewire;

use App\Exports\SummaryExport;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CollegeLabSummary extends Component
{
    use WithPagination;

    public string $activeTab = 'with_lab';

    public function showTab(string $tab): void
    {
        abort_unless(in_array($tab, ['with_lab', 'without_lab'], true), 404);

        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function export(string $tab): BinaryFileResponse
    {
        [$rows, $headings, $filename] = match ($tab) {
            'with_lab' => [
                $this->colleges()->where('has_lab', 1)->values()->map(
                    fn (Teacher $college, int $index): array => [
                        $index + 1,
                        $college->college_code,
                        $college->college_name ?? '-',
                        (int) $college->total_computers,
                    ],
                )->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'কম্পিউটারের সংখ্যা'],
                'colleges-with-computer-lab.xlsx',
            ],
            'without_lab' => [
                $this->colleges()->where('has_lab', 0)->values()->map(
                    fn (Teacher $college, int $index): array => [
                        $index + 1,
                        $college->college_code,
                        $college->college_name ?? '-',
                        'ল্যাব নেই',
                    ],
                )->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'অবস্থা'],
                'colleges-without-computer-lab.xlsx',
            ],
            default => abort(404),
        };

        return Excel::download(new SummaryExport($rows, $headings), $filename);
    }

    public function render(): View
    {
        $hasLab = $this->activeTab === 'with_lab';
        $colleges = $this->collegesQuery($hasLab)->paginate(50);

        return view('livewire.college-lab-summary', [
            'colleges' => $colleges,
        ]);
    }

    private function collegesQuery(bool $hasLab): Builder
    {
        $labCondition = "MAX(CASE WHEN LOWER(has_computer_lab) = 'yes' THEN 1 ELSE 0 END)";

        return Teacher::select(
            'college_code',
            'college_name',
            DB::raw("{$labCondition} as has_lab"),
            DB::raw('MAX(computer_count) as total_computers')
        )
            ->whereNotNull('college_code')
            ->groupBy('college_code', 'college_name')
            ->havingRaw("{$labCondition} = ?", [$hasLab ? 1 : 0])
            ->orderBy('college_code');
    }

    /**
     * @return Collection<int, Teacher>
     */
    private function colleges(): Collection
    {
        return $this->collegesQuery(true)->get()
            ->merge($this->collegesQuery(false)->get())
            ->sortBy('college_code')
            ->values();
    }
}
