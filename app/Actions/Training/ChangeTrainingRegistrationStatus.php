<?php

namespace App\Actions\Training;

use App\Enums\TrainingRegistrationStatus;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingRegistrationStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeTrainingRegistrationStatus
{
    public function __construct(
        private AddCompletedTrainingToTeacherProfile $addCompletedTrainingToTeacherProfile,
    ) {}

    public function handle(Training $training, User $participant, TrainingRegistrationStatus $newStatus, User $reviewer): void
    {
        $statusChanged = DB::transaction(function () use ($training, $participant, $newStatus, $reviewer): bool {
            $lockedTraining = Training::query()->lockForUpdate()->findOrFail($training->id);
            $registration = DB::table('training_user')
                ->where('training_id', $lockedTraining->id)
                ->where('user_id', $participant->id)
                ->lockForUpdate()
                ->first();

            abort_if($registration === null, 404);
            $currentStatus = TrainingRegistrationStatus::from($registration->status);

            if ($currentStatus === $newStatus) {
                return false;
            }

            if (! $currentStatus->canTransitionTo($newStatus)) {
                throw ValidationException::withMessages([
                    'registrationStatus' => __('A :current registration cannot be changed to :next.', [
                        'current' => __($currentStatus->value),
                        'next' => __($newStatus->value),
                    ]),
                ]);
            }

            $this->validateTransition($lockedTraining, $newStatus);
            $now = now();
            $attributes = [
                'status' => $newStatus->value,
                'approved_by' => $newStatus === TrainingRegistrationStatus::Pending ? null : $reviewer->id,
                'approved_at' => in_array($newStatus, [TrainingRegistrationStatus::Approved, TrainingRegistrationStatus::Rejected], true) ? $now : $registration->approved_at,
                'updated_at' => $now,
            ];

            if ($newStatus === TrainingRegistrationStatus::Completed) {
                $attributes['completed_at'] = $now;
                $attributes['certificate_number'] = $lockedTraining->has_certificate
                    ? sprintf('NU-TC-%d-%d-%s', $lockedTraining->id, $participant->id, $now->format('Y'))
                    : null;
            }

            DB::table('training_user')->where('id', $registration->id)->update($attributes);

            if ($newStatus === TrainingRegistrationStatus::Completed) {
                $this->addCompletedTrainingToTeacherProfile->handle($participant, $lockedTraining);
            }

            return true;
        }, attempts: 3);

        if ($statusChanged) {
            $participant->notify(new TrainingRegistrationStatusNotification($training, $newStatus->value));
        }
    }

    private function validateTransition(Training $training, TrainingRegistrationStatus $newStatus): void
    {
        if ($newStatus === TrainingRegistrationStatus::Approved && $training->capacity !== null) {
            $selectedCount = $training->participants()
                ->wherePivotIn('status', [TrainingRegistrationStatus::Approved->value, TrainingRegistrationStatus::Completed->value])
                ->count();

            if ($selectedCount >= $training->capacity) {
                throw ValidationException::withMessages([
                    'registrationStatus' => __('This training has reached its selection capacity.'),
                ]);
            }
        }

        if ($newStatus === TrainingRegistrationStatus::Completed && $training->end_date->isFuture()) {
            throw ValidationException::withMessages([
                'registrationStatus' => __('Training can only be completed after its end time.'),
            ]);
        }
    }
}
