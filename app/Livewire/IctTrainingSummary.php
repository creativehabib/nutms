<?php

namespace App\Livewire;

use App\Models\Teacher;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class IctTrainingSummary extends Component
{
    /**
     * Values that do not identify an actual training.
     *
     * @var list<string>
     */
    private const NON_TRAINING_VALUES = ['', 'n/a', 'no', '-', '---', 'nill', 'na', '0', 'no training'];

    public function render(): View
    {
        $teachersWithIct = Teacher::select('college_code', 'college_name', 'name', 'ict_training_name', 'other_training_name', 'training_institute')
            ->where(function (Builder $query): void {
                $this->whereMeaningfulTrainingName($query, 'ict_training_name')
                    ->orWhere(function (Builder $query): void {
                        $this->whereMeaningfulTrainingName($query, 'other_training_name');
                    });
            })
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->each(function (Teacher $teacher): void {
                $teacher->ict_training_name = $this->meaningfulTrainingName($teacher->ict_training_name);
                $teacher->other_training_name = $this->meaningfulTrainingName($teacher->other_training_name);
            })
            ->groupBy('college_code');

        $teachersWithoutIct = Teacher::select('college_code', 'college_name', 'name')
            ->whereIn($this->normalizedColumn('ict_training_name'), self::NON_TRAINING_VALUES)
            ->whereIn($this->normalizedColumn('other_training_name'), self::NON_TRAINING_VALUES)
            ->orderBy('college_code', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('college_code');

        return view('livewire.ict-training-summary', [
            'teachersWithIct' => $teachersWithIct,
            'teachersWithoutIct' => $teachersWithoutIct,
        ]);
    }

    private function whereMeaningfulTrainingName(Builder $query, string $column): Builder
    {
        return $query->whereNotIn($this->normalizedColumn($column), self::NON_TRAINING_VALUES);
    }

    private function normalizedColumn(string $column): Expression
    {
        return DB::raw("LOWER(TRIM(COALESCE({$column}, '')))");
    }

    private function meaningfulTrainingName(?string $trainingName): ?string
    {
        $normalizedTrainingName = Str::of($trainingName ?? '')->trim()->lower()->toString();

        return in_array($normalizedTrainingName, self::NON_TRAINING_VALUES, true) ? null : $trainingName;
    }
}
