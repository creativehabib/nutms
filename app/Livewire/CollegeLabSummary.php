<?php

namespace App\Livewire;

use App\Exports\SummaryExport;
use App\Models\College;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
                $this->colleges(true)->map(
                    fn (College $college, int $index): array => [
                        $index + 1,
                        $college->college_code,
                        $college->name,
                        (int) $college->total_computers,
                    ],
                )->all(),
                ['ক্র.নং', 'কলেজ কোড', 'কলেজের নাম', 'কম্পিউটারের সংখ্যা'],
                'colleges-with-computer-lab.xlsx',
            ],
            'without_lab' => [
                $this->colleges(false)->map(
                    fn (College $college, int $index): array => [
                        $index + 1,
                        $college->college_code,
                        $college->name,
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
        ])->layout('layouts.app', ['title' => 'Lab Summary']);
    }

    private function collegesQuery(bool $hasLab): Builder
    {
        return College::query()
            ->select(['id', 'college_code', 'name'])
            ->selectRaw('(COALESCE(desktop_count, 0) + COALESCE(laptop_count, 0)) as total_computers')
            ->where('is_active', true)
            ->when($hasLab, function (Builder $query) {
                $query->where('has_computer_lab', true);
            }, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('has_computer_lab', false)
                        ->orWhereNull('has_computer_lab');
                });
            })
            ->orderBy('college_code')
            ->orderBy('name');
    }

    /**
     * @return Collection<int, College>
     */
    private function colleges(bool $hasLab): Collection
    {
        return $this->collegesQuery($hasLab)->get();
    }
}
