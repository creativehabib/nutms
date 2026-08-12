<?php

namespace App\Livewire\Training;

use App\Enums\ApprovalStatus;
use App\Models\Training;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpcomingTrainings extends Component
{
    public int $days = 30;

    public function enroll(int $trainingId): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->isRegisteredAffiliatedCollegeTeacher(auth()->user()), 403);

        $training = Training::query()
            ->where('status', 'Upcoming')
            ->where('start_date', '>', now())
            ->findOrFail($trainingId);

        if (! $training->allows_repeat && $training->training_type_id !== null && auth()->user()->trainings()
            ->where('trainings.training_type_id', $training->training_type_id)
            ->wherePivot('status', 'Completed')
            ->exists()) {
            Flux::toast(variant: 'warning', text: __('You have already completed this training.'));

            return;
        }

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
                ->when(
                    ! auth()->check() || ! $this->isRegisteredAffiliatedCollegeTeacher(auth()->user()),
                    fn ($query) => $query->whereRaw('1 = 0'),
                )
                ->orderBy('start_date')
                ->limit(6)
                ->get(),
            'registrations' => auth()->check()
                ? auth()->user()->trainings()->pluck('training_user.status', 'trainings.id')->all()
                : [],
        ]);
    }

    private function isRegisteredAffiliatedCollegeTeacher(User $user): bool
    {
        return $user->hasRole('teacher')
            && $user->teacherProfile()
                ->where('approval_status', ApprovalStatus::Approved)
                ->whereHas('college', fn ($query) => $query
                    ->where('approval_status', ApprovalStatus::Approved)
                    ->where('is_active', true))
                ->exists();
    }
}
