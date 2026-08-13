<?php

namespace App\Livewire\Training;

use App\Actions\Training\RegisterTeacherForTraining;
use App\Enums\ApprovalStatus;
use App\Models\Training;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpcomingTrainings extends Component
{
    private RegisterTeacherForTraining $registerTeacherForTraining;

    public int $days = 30;

    public bool $compact = false;

    public function boot(RegisterTeacherForTraining $registerTeacherForTraining): void
    {
        $this->registerTeacherForTraining = $registerTeacherForTraining;
    }

    public function enroll(int $trainingId): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->isRegisteredAffiliatedCollegeTeacher(auth()->user()), 403);

        $this->registerTeacherForTraining->handle(Training::query()->findOrFail($trainingId), auth()->user());
        Flux::toast(variant: 'success', text: __('Your registration is waiting for admin approval.'));
    }

    public function render(): View
    {
        return view('livewire.training.upcoming-trainings', [
            'trainings' => Training::query()
                ->withCount([
                    'participants as active_participants_count' => fn ($query) => $query
                        ->whereIn('training_user.status', ['Approved', 'Completed']),
                ])
                ->where('status', 'Upcoming')
                ->whereBetween('start_date', [now(), now()->addDays($this->days)->endOfDay()])
                ->when(
                    ! auth()->check() || ! $this->isRegisteredAffiliatedCollegeTeacher(auth()->user()),
                    fn ($query) => $query->whereRaw('1 = 0'),
                )
                ->orderBy('start_date')
                ->limit($this->compact ? 3 : 6)
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
