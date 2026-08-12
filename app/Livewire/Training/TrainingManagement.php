<?php

namespace App\Livewire\Training;

use App\Models\Training;
use App\Models\TrainingType;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingManagement extends Component
{
    use WithPagination;

    public bool $showTrainingModal = false;
    public ?int $editingTrainingId = null;
    public string $trainingTypeId = '';
    public string $description = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $registrationDeadline = '';
    public string $type = 'Offline';
    public string $locationOrLink = '';
    public string $instructorName = '';
    public string $capacity = '';
    public bool $hasCertificate = true;
    public bool $allowsRepeat = false;
    public string $status = 'Upcoming';

    public function save(): void
    {
        $this->authorize('training-catalog.manage');

        $validated = $this->validate([
            'trainingTypeId' => ['required', Rule::exists('training_types', 'id')->where('is_active', true)],
            'description' => ['nullable', 'string', 'max:5000'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'registrationDeadline' => ['required', 'date', 'before:startDate'],
            'type' => ['required', Rule::in(['Online', 'Offline', 'Hybrid'])],
            'locationOrLink' => ['nullable', 'url:http,https', 'max:500'],
            'instructorName' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'hasCertificate' => ['boolean'],
            'allowsRepeat' => ['boolean'],
            'status' => ['required', Rule::in(['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'])],
        ]);

        $trainingType = TrainingType::query()->findOrFail($validated['trainingTypeId']);
        $attributes = [
            'training_type_id' => $trainingType->id,
            'title' => $trainingType->name,
            'description' => $validated['description'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'registration_deadline' => $validated['registrationDeadline'],
            'type' => $validated['type'],
            'location_or_link' => $validated['locationOrLink'],
            'instructor_name' => $validated['instructorName'],
            'capacity' => $validated['capacity'] === '' ? null : (int) $validated['capacity'],
            'has_certificate' => $validated['hasCertificate'],
            'allows_repeat' => $validated['allowsRepeat'],
            'status' => $validated['status'],
        ];

        if ($this->editingTrainingId === null) {
            $attributes['slug'] = Str::slug($trainingType->name).'-'.Str::lower(Str::random(6));
        }

        Training::query()->updateOrCreate(['id' => $this->editingTrainingId], $attributes);
        $this->resetForm();
        Flux::toast(variant: 'success', text: __('Training has been saved.'));
    }

    public function edit(int $trainingId): void
    {
        $this->authorize('training-catalog.manage');
        $training = Training::query()->findOrFail($trainingId);

        $this->editingTrainingId = $training->id;
        $this->trainingTypeId = $training->training_type_id === null ? '' : (string) $training->training_type_id;
        $this->description = $training->description ?? '';
        $this->startDate = $training->start_date->format('Y-m-d\TH:i');
        $this->endDate = $training->end_date->format('Y-m-d\TH:i');
        $this->registrationDeadline = $training->registration_deadline?->format('Y-m-d\TH:i') ?? '';
        $this->type = $training->type;
        $this->locationOrLink = $training->location_or_link ?? '';
        $this->instructorName = $training->instructor_name ?? '';
        $this->capacity = $training->capacity === null ? '' : (string) $training->capacity;
        $this->hasCertificate = $training->has_certificate;
        $this->allowsRepeat = $training->allows_repeat;
        $this->status = $training->status;
        $this->showTrainingModal = true;
    }

    public function create(): void
    {
        $this->authorize('training-catalog.manage');
        $this->resetForm();
        $this->showTrainingModal = true;
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
        $registration = $training->participants()->whereKey($userId)->wherePivot('status', 'Approved')->firstOrFail();

        $training->participants()->updateExistingPivot($registration->id, [
            'status' => 'Completed',
            'completed_at' => now(),
            'certificate_number' => $training->has_certificate
                ? 'NU-TC-'.$training->id.'-'.$registration->id.'-'.now()->format('Y')
                : null,
        ]);
        Flux::toast(variant: 'success', text: __('The training was added to the teacher profile.'));
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingTrainingId', 'trainingTypeId', 'description', 'startDate', 'endDate',
            'registrationDeadline', 'locationOrLink', 'instructorName', 'capacity',
            'showTrainingModal',
        ]);
        $this->type = 'Offline';
        $this->status = 'Upcoming';
        $this->hasCertificate = true;
        $this->allowsRepeat = false;
    }

    public function render(): View
    {
        $this->authorize('training-catalog.manage');

        return view('livewire.training.training-management', [
            'trainingTypes' => TrainingType::query()
                ->with('trainingInstitute:id,name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'trainings' => Training::query()
                ->withCount('participants')
                ->latest('start_date')
                ->paginate(10),
        ])->layout('layouts.app', ['title' => __('Training Management')]);
    }

    private function updateRegistration(int $trainingId, int $userId, string $status): void
    {
        $this->authorize('training-catalog.manage');
        $training = Training::query()->findOrFail($trainingId);
        $registration = $training->participants()->whereKey($userId)->wherePivot('status', 'Pending')->firstOrFail();

        $training->participants()->updateExistingPivot($registration->id, [
            'status' => $status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        Flux::toast(variant: 'success', text: __("Registration {$status}."));
    }
}
