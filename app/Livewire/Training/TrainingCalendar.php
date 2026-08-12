<?php

namespace App\Livewire\Training;

use App\Models\Training;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class TrainingCalendar extends Component
{
    public int $currentYear;
    public int $currentMonth;
    public string $type = 'All';

    public function mount(): void
    {
        $this->goToToday();
    }

    public function nextMonth(): void
    {
        $this->setCurrentMonth($this->month()->addMonth());
    }

    public function previousMonth(): void
    {
        $this->setCurrentMonth($this->month()->subMonth());
    }

    public function goToToday(): void
    {
        $this->setCurrentMonth(CarbonImmutable::today());
    }

    public function render(): View
    {
        $month = $this->month();
        $trainings = Training::query()
            ->whereNotIn('status', ['Draft', 'Canceled'])
            ->whereBetween('start_date', [$month, $month->endOfMonth()->endOfDay()])
            ->when($this->type !== 'All', fn ($query) => $query->where('type', $this->type))
            ->orderBy('start_date')
            ->get();

        return view('livewire.training.training-calendar', [
            'month' => $month,
            'daysInMonth' => $month->daysInMonth,
            'firstDayOfWeek' => $month->dayOfWeek,
            'trainingsByDate' => $this->groupTrainingsByDate($trainings),
        ])->layout('layouts.app', ['title' => __('Training Calendar')]);
    }

    private function month(): CarbonImmutable
    {
        return CarbonImmutable::create($this->currentYear, $this->currentMonth, 1)->startOfDay();
    }

    private function setCurrentMonth(CarbonImmutable $date): void
    {
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    /** @param Collection<int, Training> $trainings */
    private function groupTrainingsByDate(Collection $trainings): Collection
    {
        return $trainings->groupBy(fn (Training $training): string => $training->start_date->toDateString());
    }
}
