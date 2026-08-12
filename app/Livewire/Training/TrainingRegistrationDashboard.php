<?php

namespace App\Livewire\Training;

use App\Models\Training;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TrainingRegistrationDashboard extends Component
{
    public string $registrationStatus = 'All';

    public function approve(int $trainingId, int $userId): void
    {
        $this->updateRegistration($trainingId, $userId, 'Approved');
    }

    public function reject(int $trainingId, int $userId): void
    {
        $this->updateRegistration($trainingId, $userId, 'Rejected');
    }

    public function complete(int $trainingId, int $userId): void
    {
        $this->authorize('training-catalog.manage');
        $training = Training::query()->where('end_date', '<=', now())->findOrFail($trainingId);
        $participant = $training->participants()->whereKey($userId)->wherePivot('status', 'Approved')->firstOrFail();

        $training->participants()->updateExistingPivot($participant->id, [
            'status' => 'Completed',
            'completed_at' => now(),
            'certificate_number' => $training->has_certificate
                ? 'NU-TC-'.$training->id.'-'.$participant->id.'-'.now()->format('Y')
                : null,
        ]);
        Flux::toast(variant: 'success', text: __('Training marked as completed.'));
    }

    public function updateTrainingStatus(int $trainingId, string $status): void
    {
        $this->authorize('training-catalog.manage');
        validator(['status' => $status], [
            'status' => ['required', Rule::in(['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'])],
        ])->validate();

        Training::query()->findOrFail($trainingId)->update(['status' => $status]);
        Flux::toast(variant: 'success', text: __('Training status updated.'));
    }

    public function render(): View
    {
        $this->authorize('training-catalog.manage');

        return view('livewire.training.training-registration-dashboard', [
            'trainings' => Training::query()
                ->whereHas('participants', fn ($query) => $query
                    ->when($this->registrationStatus !== 'All', fn ($statusQuery) => $statusQuery
                        ->where('training_user.status', $this->registrationStatus)))
                ->with(['participants' => fn ($query) => $query
                    ->when($this->registrationStatus !== 'All', fn ($statusQuery) => $statusQuery
                        ->where('training_user.status', $this->registrationStatus))
                    ->with('teacherProfile.college:id,name')])
                ->withCount([
                    'participants as pending_registrations_count' => fn ($query) => $query->where('training_user.status', 'Pending'),
                    'participants as approved_registrations_count' => fn ($query) => $query->where('training_user.status', 'Approved'),
                ])
                ->latest('start_date')
                ->limit(8)
                ->get(),
        ]);
    }

    private function updateRegistration(int $trainingId, int $userId, string $status): void
    {
        $this->authorize('training-catalog.manage');
        $training = Training::query()->findOrFail($trainingId);
        $participant = $training->participants()->whereKey($userId)->wherePivot('status', 'Pending')->firstOrFail();

        $training->participants()->updateExistingPivot($participant->id, [
            'status' => $status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        Flux::toast(variant: 'success', text: __('Registration status updated.'));
    }
}
