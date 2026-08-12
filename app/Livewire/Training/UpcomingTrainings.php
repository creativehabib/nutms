<?php

namespace App\Livewire\Training;

use App\Models\Training;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpcomingTrainings extends Component
{
    public int $days = 30;

    public function enroll(int $trainingId): void
    {
        abort_unless(auth()->check(), 403);

        $training = Training::query()
            ->where('status', 'Upcoming')
            ->where('start_date', '>', now())
            ->findOrFail($trainingId);

        if ($training->registration_deadline?->isPast()) {
            Flux::toast(variant: 'warning', text: __('Registration for this training has closed.'));

            return;
        }

        if ($training->capacity !== null && $training->participants()->count() >= $training->capacity) {
            Flux::toast(variant: 'warning', text: __('No seats are currently available.'));

            return;
        }

        $training->participants()->syncWithoutDetaching([auth()->id()]);
        Flux::toast(variant: 'success', text: __('You are registered for this training.'));
    }

    public function render(): View
    {
        return view('livewire.training.upcoming-trainings', [
            'trainings' => Training::query()
                ->withCount('participants')
                ->where('status', 'Upcoming')
                ->whereBetween('start_date', [now(), now()->addDays($this->days)->endOfDay()])
                ->orderBy('start_date')
                ->limit(6)
                ->get(),
            'enrolledTrainingIds' => auth()->check()
                ? auth()->user()->trainings()->pluck('trainings.id')->all()
                : [],
        ]);
    }
}
