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
            ->whereHas('eligibleTeachers', fn ($query) => $query->whereKey(auth()->id()))
            ->findOrFail($trainingId);

        if ($training->registration_deadline?->isPast()) {
            Flux::toast(variant: 'warning', text: __('Registration for this training has closed.'));

            return;
        }

        $activeRegistrationCount = $training->participants()
            ->wherePivotIn('status', ['Pending', 'Approved', 'Completed'])
            ->count();

        if ($training->capacity !== null && $activeRegistrationCount >= $training->capacity) {
            Flux::toast(variant: 'warning', text: __('No seats are currently available.'));

            return;
        }

        $training->participants()->syncWithoutDetaching([
            auth()->id() => ['status' => 'Pending'],
        ]);
        Flux::toast(variant: 'success', text: __('Your registration is waiting for admin approval.'));
    }

    public function render(): View
    {
        return view('livewire.training.upcoming-trainings', [
            'trainings' => Training::query()
                ->withCount([
                    'participants as active_participants_count' => fn ($query) => $query
                        ->whereIn('training_user.status', ['Pending', 'Approved', 'Completed']),
                ])
                ->where('status', 'Upcoming')
                ->whereBetween('start_date', [now(), now()->addDays($this->days)->endOfDay()])
                ->when(auth()->check(), fn ($query) => $query->whereHas(
                    'eligibleTeachers',
                    fn ($eligibleQuery) => $eligibleQuery->whereKey(auth()->id()),
                ))
                ->orderBy('start_date')
                ->limit(6)
                ->get(),
            'registrations' => auth()->check()
                ? auth()->user()->trainings()->pluck('training_user.status', 'trainings.id')->all()
                : [],
        ]);
    }
}
