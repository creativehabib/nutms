<?php

namespace App\Livewire\Training;

use App\Actions\Training\AddCompletedTrainingToTeacherProfile;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingRegistrationStatusNotification;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingRegistrationManagement extends Component
{
    use WithPagination;

    public string $registrationStatus = 'All';

    public bool $showRegistrationModal = false;

    public bool $showDeleteModal = false;

    #[Locked]
    public ?int $selectedTrainingId = null;

    #[Locked]
    public ?int $selectedUserId = null;

    public function updatedRegistrationStatus(): void
    {
        $this->resetPage();
    }

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
        app(AddCompletedTrainingToTeacherProfile::class)->handle($participant, $training);
        $participant->notify(new TrainingRegistrationStatusNotification($training, 'Completed'));
        Flux::toast(variant: 'success', text: __('Training marked as completed.'));
    }

    public function updateRegistrationStatus(int $trainingId, int $userId, string $status): void
    {
        $this->authorize('training-catalog.manage');
        validator(['status' => $status], [
            'status' => ['required', Rule::in(['Pending', 'Approved', 'Rejected'])],
        ])->validate();

        $training = Training::query()->findOrFail($trainingId);
        $participant = $training->participants()->whereKey($userId)->firstOrFail();
        $training->participants()->updateExistingPivot($participant->id, [
            'status' => $status,
            'approved_by' => $status === 'Pending' ? null : auth()->id(),
            'approved_at' => $status === 'Pending' ? null : now(),
        ]);
        $this->notifyParticipant($participant, $training, $status);
        Flux::toast(variant: 'success', text: __('Registration status updated.'));
    }

    public function viewRegistration(int $trainingId, int $userId): void
    {
        $this->authorize('training-catalog.manage');
        $this->setSelectedRegistration($trainingId, $userId);
        $this->showRegistrationModal = true;
    }

    public function confirmDelete(int $trainingId, int $userId): void
    {
        $this->authorize('training-catalog.manage');
        $this->setSelectedRegistration($trainingId, $userId);
        $this->showDeleteModal = true;
    }

    public function deleteRegistration(): void
    {
        $this->authorize('training-catalog.manage');
        abort_if($this->selectedTrainingId === null || $this->selectedUserId === null, 404);

        Training::query()->findOrFail($this->selectedTrainingId)
            ->participants()
            ->detach($this->selectedUserId);

        $this->resetSelection();
        Flux::toast(variant: 'success', text: __('Registration deleted.'));
    }

    public function render(): View
    {
        $this->authorize('training-catalog.manage');

        return view('livewire.training.training-registration-management', [
            'trainings' => Training::query()
                ->whereNotIn('status', ['Draft', 'Canceled'])
                ->whereHas('participants', fn ($query) => $query
                    ->where('training_user.status', '!=', 'Completed')
                    ->when($this->registrationStatus !== 'All', fn ($statusQuery) => $statusQuery
                        ->where('training_user.status', $this->registrationStatus)))
                ->with(['participants' => fn ($query) => $query
                    ->where('training_user.status', '!=', 'Completed')
                    ->when($this->registrationStatus !== 'All', fn ($statusQuery) => $statusQuery
                        ->where('training_user.status', $this->registrationStatus))
                    ->with('teacherProfile.college:id,name')])
                ->withCount([
                    'participants as pending_registrations_count' => fn ($query) => $query->where('training_user.status', 'Pending'),
                    'participants as approved_registrations_count' => fn ($query) => $query->where('training_user.status', 'Approved'),
                ])
                ->latest('start_date')
                ->paginate(10),
            'selectedRegistration' => $this->selectedRegistration(),
        ])->layout('layouts.app', ['title' => __('Registered Teachers')]);
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
        $this->notifyParticipant($participant, $training, $status);
        Flux::toast(variant: 'success', text: __('Registration status updated.'));
    }

    private function notifyParticipant(User $participant, Training $training, string $status): void
    {
        if (in_array($status, ['Approved', 'Rejected'], true)) {
            $participant->notify(new TrainingRegistrationStatusNotification($training, $status));
        }
    }

    private function setSelectedRegistration(int $trainingId, int $userId): void
    {
        Training::query()->whereHas('participants', fn ($query) => $query->whereKey($userId))->findOrFail($trainingId);
        $this->selectedTrainingId = $trainingId;
        $this->selectedUserId = $userId;
    }

    private function selectedRegistration(): ?array
    {
        if ($this->selectedTrainingId === null || $this->selectedUserId === null) {
            return null;
        }

        $training = Training::query()->find($this->selectedTrainingId);
        $participant = $training?->participants()->with('teacherProfile.college:id,name')->whereKey($this->selectedUserId)->first();

        return $training !== null && $participant !== null ? ['training' => $training, 'participant' => $participant] : null;
    }

    public function resetSelection(): void
    {
        $this->reset('showRegistrationModal', 'showDeleteModal', 'selectedTrainingId', 'selectedUserId');
    }
}
